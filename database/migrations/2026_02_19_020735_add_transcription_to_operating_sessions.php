<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operating_sessions', function (Blueprint $table) {
            $table->boolean('is_transcription')->default(false)->after('qso_count');
            $table->foreignId('band_id')->nullable()->change();
            $table->foreignId('mode_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('operating_sessions', function (Blueprint $table) {
            $table->dropColumn('is_transcription');
            $table->foreignId('band_id')->nullable(false)->change();
            $table->foreignId('mode_id')->nullable(false)->change();
        });
    }
};
