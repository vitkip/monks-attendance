<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE monks MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'active'");

        DB::table('monks')->where('status', '1')->update(['status' => 'active']);
        DB::table('monks')->where('status', '0')->update(['status' => 'disrobed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('monks')->where('status', 'active')->update(['status' => '1']);
        DB::table('monks')->whereIn('status', ['disrobed', 'transferred'])->update(['status' => '0']);

        DB::statement("ALTER TABLE monks MODIFY COLUMN status TINYINT NOT NULL DEFAULT 1");
    }
};
