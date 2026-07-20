<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monks', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->after('photo');
            $table->date('ordination_date')->nullable()->after('birth_date');
        });
    }

    public function down(): void
    {
        Schema::table('monks', function (Blueprint $table) {
            $table->dropColumn(['birth_date', 'ordination_date']);
        });
    }
};
