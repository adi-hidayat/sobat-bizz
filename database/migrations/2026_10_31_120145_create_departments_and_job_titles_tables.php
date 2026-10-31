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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('job_titles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->foreignId('department_id')
                ->nullable()
                ->after('user_id')
                ->constrained()
                ->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('job_title_id')
                ->nullable()
                ->after('phone')
                ->constrained()
                ->nullOnDelete();
        });

        $this->migrateDepartmentValues();
        $this->migrateJobTitleValues();

        Schema::table('staff', function (Blueprint $table) {
            $table->dropIndex(['department']);
            $table->dropColumn('department');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('job_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('department', 100)->nullable()->after('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('job_title')->nullable()->after('phone');
        });

        $this->restoreDepartmentValues();
        $this->restoreJobTitleValues();

        Schema::table('staff', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->index('department');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_title_id');
        });

        Schema::dropIfExists('job_titles');
        Schema::dropIfExists('departments');
    }

    /**
     * Pindahkan nilai departemen lama pada tabel staff menjadi master departemen.
     */
    private function migrateDepartmentValues(): void
    {
        $names = DB::table('staff')
            ->whereNotNull('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        foreach ($names as $name) {
            $departmentId = DB::table('departments')->insertGetId([
                'name' => $name,
                'is_active' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('staff')->where('department', $name)->update(['department_id' => $departmentId]);
        }
    }

    /**
     * Pindahkan nilai jabatan lama pada tabel users menjadi master jabatan.
     */
    private function migrateJobTitleValues(): void
    {
        $names = DB::table('users')
            ->whereNotNull('job_title')
            ->distinct()
            ->orderBy('job_title')
            ->pluck('job_title');

        foreach ($names as $name) {
            $jobTitleId = DB::table('job_titles')->insertGetId([
                'name' => $name,
                'is_active' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')->where('job_title', $name)->update(['job_title_id' => $jobTitleId]);
        }
    }

    private function restoreDepartmentValues(): void
    {
        foreach (DB::table('departments')->pluck('name', 'id') as $id => $name) {
            DB::table('staff')->where('department_id', $id)->update(['department' => $name]);
        }
    }

    private function restoreJobTitleValues(): void
    {
        foreach (DB::table('job_titles')->pluck('name', 'id') as $id => $name) {
            DB::table('users')->where('job_title_id', $id)->update(['job_title' => $name]);
        }
    }
};
