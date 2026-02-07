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
        Schema::table('event_configurations', function (Blueprint $table) {
            $table->boolean('guestbook_enabled')->default(false);
            $table->decimal('guestbook_latitude', 10, 7)->nullable();
            $table->decimal('guestbook_longitude', 10, 7)->nullable();
            $table->integer('guestbook_detection_radius')->default(500);
            $table->json('guestbook_local_subnets')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_configurations', function (Blueprint $table) {
            if (Schema::hasColumns('event_configurations', [
                'guestbook_enabled',
                'guestbook_latitude',
                'guestbook_longitude',
                'guestbook_detection_radius',
                'guestbook_local_subnets',
            ])) {
                $table->dropColumn([
                    'guestbook_enabled',
                    'guestbook_latitude',
                    'guestbook_longitude',
                    'guestbook_detection_radius',
                    'guestbook_local_subnets',
                ]);
            }
        });
    }
};
