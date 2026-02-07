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
        Schema::table('guestbook_entries', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->enum('presence_type', ['in_person', 'online'])->default('online');
            $table->enum('visitor_category', ['elected_official', 'arrl_official', 'agency', 'media', 'ares_races', 'ham_club', 'youth', 'general_public'])->default('general_public');
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->softDeletes();

            $table->index('presence_type');
            $table->index('visitor_category');
            $table->index('is_verified');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guestbook_entries', function (Blueprint $table) {
            $table->dropForeignIdFor('users', 'user_id');
            $table->dropForeignIdFor('users', 'verified_by');
            $table->dropIndex(['presence_type']);
            $table->dropIndex(['visitor_category']);
            $table->dropIndex(['is_verified']);
            $table->dropIndex(['user_id']);
            $table->dropColumn(['user_id', 'presence_type', 'visitor_category', 'is_verified', 'verified_by', 'verified_at', 'deleted_at']);
        });
    }
};
