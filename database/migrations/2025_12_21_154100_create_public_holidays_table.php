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
        Schema::create('public_holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('name');
            $table->string('local_name')->nullable();
            $table->string('country_code', 2)->default('MY');
            $table->year('year')->index();
            $table->boolean('is_fixed')->default(false); // Fixed holidays like New Year
            $table->timestamps();
            
            // Unique constraint to prevent duplicate entries
            $table->unique(['date', 'country_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_holidays');
    }
};
