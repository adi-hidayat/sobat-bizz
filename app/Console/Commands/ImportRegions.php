<?php

namespace App\Console\Commands;

use App\Models\Region;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PDO;
use RuntimeException;
use Symfony\Component\Process\Process;

class ImportRegions extends Command
{
    protected $signature = 'crm:import-regions {--keep-staging : Jangan hapus database staging setelah selesai}';

    protected $description = 'Import wilayah global (dr5hn) dan Indonesia (cahyadsn) ke tabel regions';

    private const STAGING_DATABASE = 'crm_geo_staging';

    /**
     * @var array<string, mixed>
     */
    private array $connection;

    public function handle(): int
    {
        if (config('database.default') !== 'mysql') {
            $this->error('Perintah ini hanya mendukung koneksi MySQL.');

            return self::FAILURE;
        }

        $this->connection = config('database.connections.mysql');
        $directory = database_path('data/regions');

        $required = [
            'regions.sql',
            'subregions.sql',
            'countries.sql',
            'states.sql',
            'counties.sql',
            'county_states.sql',
            'cities.sql',
            'wilayah.sql',
            'wilayah_kodepos.sql',
        ];

        foreach ($required as $file) {
            if (! File::exists($directory.DIRECTORY_SEPARATOR.$file)) {
                $this->error("File {$file} belum ada. Jalankan: php artisan crm:download-regions");

                return self::FAILURE;
            }
        }

        $this->line('Menyiapkan database staging ...');
        $this->createStagingDatabase();

        try {
            foreach ($required as $file) {
                $this->line("Import {$file} ke staging ...");
                $this->importSqlFile($directory.DIRECTORY_SEPARATOR.$file);
            }

            $this->line('Mengosongkan tabel regions ...');
            $this->resetRegions();

            $this->line('Import negara ...');
            $countries = $this->importCountries();
            $this->info("  {$countries} negara");

            $this->line('Import state/kota global ...');
            $states = $this->importGlobalStates();
            $cities = $this->importGlobalCities();
            $this->info("  {$states} state, {$cities} kota");

            $this->line('Import wilayah Indonesia ...');
            [$provinces, $regencies, $districts, $villages] = $this->importIndonesia();
            $this->info("  {$provinces} provinsi, {$regencies} kab/kota, {$districts} kecamatan, {$villages} desa/kelurahan");
        } finally {
            if (! $this->option('keep-staging')) {
                $this->line('Menghapus database staging ...');
                $this->dropStagingDatabase();
            }
        }

        $this->newLine();
        $this->info('Import wilayah selesai.');

        return self::SUCCESS;
    }

    private function pdo(?string $database = null): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s%s',
            $this->connection['host'],
            $this->connection['port'],
            $database !== null ? ';dbname='.$database : '',
        );

        return new PDO($dsn, $this->connection['username'], $this->connection['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => true,
        ]);
    }

    private function createStagingDatabase(): void
    {
        $this->pdo()->exec(sprintf(
            'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
            self::STAGING_DATABASE,
        ));
    }

    private function dropStagingDatabase(): void
    {
        $this->pdo()->exec(sprintf('DROP DATABASE IF EXISTS `%s`', self::STAGING_DATABASE));
    }

    private function importSqlFile(string $file): void
    {
        $process = new Process(
            [
                $this->mysqlBinary(),
                '--host='.$this->connection['host'],
                '--port='.$this->connection['port'],
                '--user='.$this->connection['username'],
                '--database='.self::STAGING_DATABASE,
                '--default-character-set=utf8mb4',
            ],
            null,
            ['MYSQL_PWD' => (string) $this->connection['password']],
            null,
            3600,
        );

        $input = fopen($file, 'r');

        if ($input === false) {
            throw new RuntimeException("Tidak dapat membaca {$file}");
        }

        $process->setInput($input);
        $process->run();

        fclose($input);

        if (! $process->isSuccessful()) {
            throw new RuntimeException("Gagal import {$file}: ".$process->getErrorOutput());
        }
    }

    private function mysqlBinary(): string
    {
        $candidates = [
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysql.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.4\\bin\\mysql.exe',
            'C:\\xampp\\mysql\\bin\\mysql.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0\\bin\\mysql.exe',
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        $command = DIRECTORY_SEPARATOR === '\\' ? 'where mysql 2>NUL' : 'which mysql 2>/dev/null';
        $path = trim((string) shell_exec($command));

        if ($path !== '') {
            return (string) strtok($path, "\r\n");
        }

        throw new RuntimeException('mysql.exe tidak ditemukan. Pasang MySQL client atau tambahkan ke PATH.');
    }

    private function resetRegions(): void
    {
        DB::table('addresses')->update([
            'country_id' => null,
            'state_id' => null,
            'city_id' => null,
            'district_id' => null,
            'village_id' => null,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('regions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function importCountries(): int
    {
        DB::statement(sprintf(
            "INSERT INTO regions (level, code, name, source, created_at, updated_at)
             SELECT 'country', c.iso2, c.name, 'dr5hn', NOW(), NOW()
             FROM `%s`.`countries` c",
            self::STAGING_DATABASE,
        ));

        DB::statement(sprintf(
            "UPDATE regions r
             JOIN `%s`.`countries` c ON r.code = c.iso2
             SET r.country_id = r.id
             WHERE r.level = 'country' AND r.source = 'dr5hn'",
            self::STAGING_DATABASE,
        ));

        return Region::query()->countries()->count();
    }

    private function importGlobalStates(): int
    {
        DB::statement(sprintf(
            "INSERT INTO regions (parent_id, country_id, level, code, name, source, created_at, updated_at)
             SELECT cr.id, cr.id, 'state', CAST(s.id AS CHAR), s.name, 'dr5hn', NOW(), NOW()
             FROM `%s`.`states` s
             JOIN `%s`.`countries` c ON c.id = s.country_id
             JOIN regions cr ON cr.level = 'country' AND cr.source = 'dr5hn' AND cr.code = c.iso2
             WHERE c.iso2 <> 'ID'",
            self::STAGING_DATABASE,
            self::STAGING_DATABASE,
        ));

        return Region::query()->where('source', 'dr5hn')->states()->count();
    }

    private function importGlobalCities(): int
    {
        DB::statement(sprintf(
            "INSERT INTO regions (parent_id, country_id, level, code, name, latitude, longitude, source, created_at, updated_at)
             SELECT sr.id, cr.id, 'city', CAST(ci.id AS CHAR), ci.name, ci.latitude, ci.longitude, 'dr5hn', NOW(), NOW()
             FROM `%s`.`cities` ci
             JOIN `%s`.`countries` c ON c.id = ci.country_id
             JOIN regions cr ON cr.level = 'country' AND cr.source = 'dr5hn' AND cr.code = c.iso2
             LEFT JOIN regions sr ON sr.level = 'state' AND sr.source = 'dr5hn' AND sr.code = CAST(ci.state_id AS CHAR)
             WHERE c.iso2 <> 'ID'",
            self::STAGING_DATABASE,
            self::STAGING_DATABASE,
        ));

        return Region::query()->where('source', 'dr5hn')->cities()->count();
    }

    /**
     * @return array{0: int, 1: int, 2: int, 3: int}
     */
    private function importIndonesia(): array
    {
        $indonesiaId = Region::query()
            ->countries()
            ->where('source', 'dr5hn')
            ->where('code', 'ID')
            ->value('id');

        if ($indonesiaId === null) {
            throw new RuntimeException('Region Indonesia tidak ditemukan setelah import negara.');
        }

        $postalCodes = DB::table(self::STAGING_DATABASE.'.wilayah_kodepos')
            ->pluck('kodepos', 'kode')
            ->all();

        $rows = DB::table(self::STAGING_DATABASE.'.wilayah')
            ->orderBy('kode')
            ->get(['kode', 'nama']);

        $byLevel = [2 => [], 4 => [], 6 => [], 10 => []];

        foreach ($rows as $row) {
            $length = strlen(str_replace('.', '', (string) $row->kode));

            if (isset($byLevel[$length])) {
                $byLevel[$length][] = $row;
            }
        }

        $this->insertLevel(
            $byLevel[2],
            Region::LEVEL_STATE,
            static fn (): ?int => null,
            $indonesiaId,
        );

        $provinceIds = Region::query()
            ->where('source', 'cahyadsn')
            ->states()
            ->pluck('id', 'code')
            ->all();

        $this->insertLevel(
            $byLevel[4],
            Region::LEVEL_CITY,
            static fn (object $row): ?int => $provinceIds[substr((string) $row->kode, 0, 2)] ?? null,
            $indonesiaId,
        );

        $regencyIds = Region::query()
            ->where('source', 'cahyadsn')
            ->cities()
            ->pluck('id', 'code')
            ->all();

        $this->insertLevel(
            $byLevel[6],
            Region::LEVEL_DISTRICT,
            static fn (object $row): ?int => $regencyIds[substr((string) $row->kode, 0, 5)] ?? null,
            $indonesiaId,
        );

        $districtIds = Region::query()
            ->where('source', 'cahyadsn')
            ->districts()
            ->pluck('id', 'code')
            ->all();

        $this->insertLevel(
            $byLevel[10],
            Region::LEVEL_VILLAGE,
            static fn (object $row): ?int => $districtIds[substr((string) $row->kode, 0, 8)] ?? null,
            $indonesiaId,
            $postalCodes,
        );

        return [
            count($byLevel[2]),
            count($byLevel[4]),
            count($byLevel[6]),
            count($byLevel[10]),
        ];
    }

    /**
     * @param  array<int, object>  $rows
     * @param  callable(object): ?int  $parentResolver
     * @param  array<string, string|null>  $postalCodes
     */
    private function insertLevel(
        array $rows,
        string $level,
        callable $parentResolver,
        int $countryId,
        array $postalCodes = [],
    ): void {
        foreach (array_chunk($rows, 1000) as $chunk) {
            $now = now();

            $payload = array_map(static fn (object $row): array => [
                'parent_id' => $parentResolver($row),
                'country_id' => $countryId,
                'level' => $level,
                'code' => (string) $row->kode,
                'name' => self::formatName((string) $row->nama),
                'postal_code' => $level === Region::LEVEL_VILLAGE
                    ? ($postalCodes[(string) $row->kode] ?? null)
                    : null,
                'source' => 'cahyadsn',
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk);

            DB::table('regions')->insert($payload);
        }
    }

    private static function formatName(string $name): string
    {
        $title = mb_convert_case(mb_strtolower(trim($name), 'UTF-8'), MB_CASE_TITLE, 'UTF-8');

        return str_replace(
            ['Dki', 'Diy', 'Ntb', 'Ntt'],
            ['DKI', 'DIY', 'NTB', 'NTT'],
            $title,
        );
    }
}
