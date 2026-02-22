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
        Schema::table('candidates', function (Blueprint $table) {
            $table->unsignedInteger('display_number')->nullable()->unique()->after('id');
        });

        // Присвоить display_number существующим кандидатам по порядку создания
        $candidates = DB::table('candidates')->orderBy('created_at')->get();
        $number = 1;
        foreach ($candidates as $candidate) {
            DB::table('candidates')
                ->where('id', $candidate->id)
                ->update(['display_number' => $number]);
            $number++;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('display_number');
        });
    }
};
