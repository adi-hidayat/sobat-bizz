<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            // Nullable karena Filament menyimpan record staff lebih dulu, lalu
            // membuat dan menghubungkan akun pengguna dari section relasinya.
            $table->foreignId('user_id')->nullable()->unique()->constrained()->cascadeOnDelete();
            $table->string('employee_number', 50)->unique();
            $table->string('department', 100)->nullable();
            $table->date('joined_at')->nullable();
            $table->timestamps();

            $table->index('department');
        });

        $this->backfillExistingUsers();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }

    /**
     * Setiap pengguna yang sudah ada sebelumnya dibuatkan data staff agar tetap tampil.
     */
    private function backfillExistingUsers(): void
    {
        DB::table('users')
            ->orderBy('id')
            ->chunkById(500, function ($users): void {
                $now = now();

                $rows = $users->map(fn (object $user): array => [
                    'user_id' => $user->id,
                    'employee_number' => 'EMP-'.str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                    'department' => null,
                    'joined_at' => $user->created_at ? substr((string) $user->created_at, 0, 10) : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                if ($rows !== []) {
                    DB::table('staff')->insert($rows);
                }
            });
    }
};
