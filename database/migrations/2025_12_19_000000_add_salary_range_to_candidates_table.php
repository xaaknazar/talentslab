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
            // Добавляем новые поля для диапазона зарплаты
            $table->decimal('expected_salary_from', 15, 2)->nullable()->after('expected_salary');
            $table->decimal('expected_salary_to', 15, 2)->nullable()->after('expected_salary_from');
            $table->string('salary_currency', 3)->default('KZT')->after('expected_salary_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn(['expected_salary_from', 'expected_salary_to', 'salary_currency']);
        });
    }
};
