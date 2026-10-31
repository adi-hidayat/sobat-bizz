<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DownloadRegionData extends Command
{
    protected $signature = 'crm:download-regions {--force : Unduh ulang walaupun file sudah ada}';

    protected $description = 'Unduh dataset wilayah global (dr5hn) dan Indonesia (cahyadsn)';

    /**
     * @var array<string, string>
     */
    private const SOURCES = [
        'regions.sql' => 'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/sql/regions.sql',
        'subregions.sql' => 'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/sql/subregions.sql',
        'countries.sql' => 'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/sql/countries.sql',
        'states.sql' => 'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/sql/states.sql',
        'counties.sql' => 'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/sql/counties.sql',
        'county_states.sql' => 'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/sql/county_states.sql',
        'cities.sql.gz' => 'https://github.com/dr5hn/countries-states-cities-database/releases/download/v5.0.0-export.1/sql-cities.sql.gz',
        'wilayah.sql' => 'https://raw.githubusercontent.com/cahyadsn/wilayah/master/db/wilayah.sql',
        'wilayah_kodepos.sql' => 'https://raw.githubusercontent.com/cahyadsn/wilayah_kodepos/main/db/wilayah_kodepos.sql',
    ];

    public function handle(): int
    {
        $directory = database_path('data/regions');
        File::ensureDirectoryExists($directory);

        foreach (self::SOURCES as $file => $url) {
            $path = $directory.DIRECTORY_SEPARATOR.$file;

            if (File::exists($path) && ! $this->option('force')) {
                $this->line("Lewati {$file} (sudah ada)");

                continue;
            }

            $this->line("Mengunduh {$file} ...");

            try {
                $response = Http::timeout(900)
                    ->withOptions(['sink' => $path])
                    ->get($url);
            } catch (\Throwable $exception) {
                throw new RuntimeException("Gagal mengunduh {$file}: {$exception->getMessage()}");
            }

            if (! $response->successful()) {
                throw new RuntimeException("Gagal mengunduh {$file}: HTTP {$response->status()}");
            }

            $this->info('  tersimpan '.number_format(File::size($path) / 1048576, 1).' MB');
        }

        $compressed = $directory.DIRECTORY_SEPARATOR.'cities.sql.gz';

        if (File::exists($compressed)) {
            $this->line('Mengekstrak cities.sql.gz ...');
            $this->gunzip($compressed, $directory.DIRECTORY_SEPARATOR.'cities.sql');
            $this->info('  cities.sql siap');
        }

        $this->newLine();
        $this->info('Semua dataset wilayah siap di database/data/regions.');

        return self::SUCCESS;
    }

    private function gunzip(string $source, string $target): void
    {
        $input = gzopen($source, 'rb');

        if ($input === false) {
            throw new RuntimeException("Tidak dapat membuka {$source}");
        }

        $output = fopen($target, 'wb');

        if ($output === false) {
            throw new RuntimeException("Tidak dapat menulis {$target}");
        }

        while (! gzeof($input)) {
            $chunk = gzread($input, 1048576);

            if ($chunk === false) {
                break;
            }

            fwrite($output, $chunk);
        }

        gzclose($input);
        fclose($output);
    }
}
