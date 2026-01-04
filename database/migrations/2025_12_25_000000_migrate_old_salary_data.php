<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Проверяем существование колонок перед миграцией данных
        if (Schema::hasColumn('candidates', 'salary_currency') &&
            Schema::hasColumn('candidates', 'expected_salary_from') &&
            Schema::hasColumn('candidates', 'expected_salary_to')) {

            // Перенос старых данных из expected_salary в новые поля
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
        // Откат не требуется, так как мы не удаляем данные
    }
};
