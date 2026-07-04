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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monk_id')->constrained('monks')->onDelete('cascade');
            $table->foreignId('fine_rate_id')->constrained('fine_rates');
            $table->date('absent_date');
            $table->string('reason', 255)->nullable();
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->tinyInteger('is_paid')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
