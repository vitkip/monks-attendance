<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE cbfol_committees MODIFY COLUMN photo TEXT NULL');
        } else {
            Schema::table('cbfol_committees', function (Blueprint $table) {
                $table->text('photo')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE cbfol_committees MODIFY COLUMN photo VARCHAR(255) NULL');
        } else {
            Schema::table('cbfol_committees', function (Blueprint $table) {
                $table->string('photo', 255)->nullable()->change();
            });
        }
    }
};
