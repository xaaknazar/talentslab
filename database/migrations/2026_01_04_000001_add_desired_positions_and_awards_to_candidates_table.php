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
            if (!Schema::hasColumn('candidates', 'desired_positions')) {
                $table->json('desired_positions')->nullable()->after('desired_position');
            }
            if (!Schema::hasColumn('candidates', 'awards')) {
                $table->json('awards')->nullable()->after('activity_sphere');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (Schema::hasColumn('candidates', 'desired_positions')) {
                $table->dropColumn('desired_positions');
            }
            if (Schema::hasColumn('candidates', 'awards')) {
                $table->dropColumn('awards');
            }
        });
    }
};
