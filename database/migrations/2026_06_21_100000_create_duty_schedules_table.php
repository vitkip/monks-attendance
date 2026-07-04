<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duty_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monk_id')->constrained('monks')->onDelete('cascade');
            $table->string('duty_name', 150);
            $table->date('duty_date');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['monk_id', 'duty_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duty_schedules');
    }
};
