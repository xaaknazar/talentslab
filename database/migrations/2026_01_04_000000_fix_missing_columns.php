<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Исправление: добавление недостающих колонок, которые не были созданы предыдущими миграциями
     */
    public function up(): void
    {
        // Добавляем salary_currency если отсутствует
        if (!Schema::hasColumn('candidates', 'salary_currency')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->string('salary_currency', 3)->default('KZT')->after('expected_salary_to');
            });
        }

        // Добавляем desired_positions если отсутствует
        if (!Schema::hasColumn('candidates', 'desired_positions')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->json('desired_positions')->nullable()->after('desired_position');
            });
        }

        // Добавляем awards если отсутствует
        if (!Schema::hasColumn('candidates', 'awards')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->json('awards')->nullable()->after('activity_sphere');
            });
        }

        // Мигрируем данные salary если колонки существуют
        if (Schema::hasColumn('candidates', 'salary_currency') &&
            Schema::hasColumn('candidates', 'expected_salary_from') &&
            Schema::hasColumn('candidates', 'expected_salary_to') &&
            Schema::hasColumn('candidates', 'expected_salary')) {

            DB::statement("
                UPDATE candidates
                SET
                    expected_salary_from = expected_salary,
                    expected_salary_to = expected_salary,
                    salary_currency = 'KZT'
                WHERE
                    expected_salary IS NOT NULL
                    AND expected_salary > 0
                    AND (expected_salary_from IS NULL OR expected_salary_to IS NULL)
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ничего не удаляем при откате
    }
};
