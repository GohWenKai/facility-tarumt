<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurring_frequency', ['daily', 'weekly', 'monthly'])->nullable();
            $table->date('recurring_end_date')->nullable();
            $table->uuid('parent_booking_id')->nullable();
            
            // Index for faster queries
            $table->index('parent_booking_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['parent_booking_id']);
            $table->dropColumn(['is_recurring', 'recurring_frequency', 'recurring_end_date', 'parent_booking_id']);
        });
    }
};
