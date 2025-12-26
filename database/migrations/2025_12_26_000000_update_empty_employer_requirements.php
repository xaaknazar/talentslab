<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Обновляем пустые поля employer_requirements
        // Устанавливаем информативное сообщение вместо NULL
        DB::table('candidates')
            ->whereNull('employer_requirements')
            ->orWhere('employer_requirements', '')
            ->update([
                'employer_requirements' => 'Не указано (требуется обновление данных)'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Откатываем изменения - ставим обратно NULL
        DB::table('candidates')
            ->where('employer_requirements', 'Не указано (требуется обновление данных)')
            ->update([
                'employer_requirements' => null
            ]);
    }
};
