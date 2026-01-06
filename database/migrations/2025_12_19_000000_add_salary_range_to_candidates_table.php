<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            // Добавляем новые поля для диапазона зарплаты (проверяем существование)
            if (!Schema::hasColumn('candidates', 'expected_salary_from')) {
                $table->decimal('expected_salary_from', 15, 2)->nullable()->after('expected_salary');
            }
            if (!Schema::hasColumn('candidates', 'expected_salary_to')) {
                $table->decimal('expected_salary_to', 15, 2)->nullable()->after('expected_salary_from');
            }
            if (!Schema::hasColumn('candidates', 'salary_currency')) {
                $table->string('salary_currency', 3)->default('KZT')->after('expected_salary_to');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (Schema::hasColumn('candidates', 'expected_salary_from')) {
                $table->dropColumn('expected_salary_from');
            }
            if (Schema::hasColumn('candidates', 'expected_salary_to')) {
                $table->dropColumn('expected_salary_to');
            }
            if (Schema::hasColumn('candidates', 'salary_currency')) {
                $table->dropColumn('salary_currency');
            }
        });
    }
};
