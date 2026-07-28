<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE monks MODIFY COLUMN type ENUM('monk', 'novice', 'nun') NOT NULL DEFAULT 'monk'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE monks MODIFY COLUMN type ENUM('monk', 'novice') NOT NULL DEFAULT 'monk'");
    }
};
