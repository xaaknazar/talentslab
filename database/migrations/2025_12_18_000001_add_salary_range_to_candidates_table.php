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
            $table->decimal('expected_salary_from', 15, 2)->nullable()->after('expected_salary');
            $table->decimal('expected_salary_to', 15, 2)->nullable()->after('expected_salary_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn(['expected_salary_from', 'expected_salary_to']);
        });
    }
};
