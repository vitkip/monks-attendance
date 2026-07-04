<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('duty_schedules', function (Blueprint $table) {
            $table->enum('schedule_type', ['once', 'weekly'])->default('once')->after('monk_id');
            $table->tinyInteger('day_of_week')->unsigned()->nullable()->after('duty_date')
                  ->comment('1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat, 7=Sun');
            $table->date('duty_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('duty_schedules', function (Blueprint $table) {
            $table->dropColumn(['schedule_type', 'day_of_week']);
            $table->date('duty_date')->nullable(false)->change();
        });
    }
};
