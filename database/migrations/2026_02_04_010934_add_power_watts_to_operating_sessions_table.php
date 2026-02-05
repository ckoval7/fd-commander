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
        Schema::table('operating_sessions', function (Blueprint $table) {
            $table->integer('power_watts')->nullable()->after('qso_count');
            $table->index('power_watts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operating_sessions', function (Blueprint $table) {
            $table->dropIndex(['power_watts']);
            $table->dropColumn('power_watts');
        });
    }
};
