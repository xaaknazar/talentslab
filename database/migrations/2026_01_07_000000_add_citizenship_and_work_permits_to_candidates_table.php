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
            // Добавляем поле гражданства (одна страна)
            if (!Schema::hasColumn('candidates', 'citizenship')) {
                $table->string('citizenship')->nullable()->after('birth_place');
            }
            // Добавляем поле разрешений на работу (массив стран)
            if (!Schema::hasColumn('candidates', 'work_permits')) {
                $table->json('work_permits')->nullable()->after('citizenship');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (Schema::hasColumn('candidates', 'citizenship')) {
                $table->dropColumn('citizenship');
            }
            if (Schema::hasColumn('candidates', 'work_permits')) {
                $table->dropColumn('work_permits');
            }
        });
    }
};
