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
        Schema::create('cbfol_committees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('title', 150)->nullable();
            $table->string('category', 50)->default('member'); // president, standing, member, advisor
            $table->string('term', 50)->default('ສະໄໝທີ IX');
            $table->string('term_year', 50)->nullable();
            $table->string('organization', 150)->nullable();
            $table->string('photo', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('bio')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('status', 20)->default('active'); // active, inactive
            $table->timestamps();

            $table->index(['category', 'sort_order']);
            $table->index(['status', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbfol_committees');
    }
};
