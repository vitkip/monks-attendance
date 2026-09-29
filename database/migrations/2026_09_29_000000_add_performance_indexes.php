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
        // 1. Monks table optimizations
        Schema::table('monks', function (Blueprint $table) {
            $table->index(['status', 'type'], 'monks_status_type_index');
            $table->index(['status', 'pansa', 'name'], 'monks_status_pansa_name_index');
            $table->index(['status', 'name'], 'monks_status_name_index');
        });

        // 2. Absences table optimizations
        Schema::table('absences', function (Blueprint $table) {
            $table->index(['is_paid', 'absent_date'], 'absences_is_paid_absent_date_index');
            $table->index(['absent_date'], 'absences_absent_date_index');
            $table->index(['monk_id', 'is_paid'], 'absences_monk_id_is_paid_index');
        });

        // 3. Duty schedules optimizations
        Schema::table('duty_schedules', function (Blueprint $table) {
            $table->index(['schedule_type', 'day_of_week'], 'duty_schedules_type_day_index');
            $table->index(['schedule_type', 'duty_date'], 'duty_schedules_type_date_index');
        });

        // 4. News optimizations
        Schema::table('news', function (Blueprint $table) {
            $table->index(['status', 'published_at'], 'news_status_published_at_index');
            $table->index(['category_id', 'status', 'published_at'], 'news_category_status_published_index');
        });

        // 5. Chants optimizations
        Schema::table('chants', function (Blueprint $table) {
            $table->index(['category_id', 'title'], 'chants_category_id_title_index');
            $table->index(['title'], 'chants_title_index');
        });

        // 6. Hero slides optimizations
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->index(['status', 'sort_order'], 'hero_slides_status_sort_order_index');
        });

        // 7. Construction transactions optimizations
        Schema::table('construction_transactions', function (Blueprint $table) {
            $table->index(['type', 'amount'], 'construction_tx_type_amount_index');
            $table->index(['construction_project_id', 'type', 'amount'], 'construction_tx_project_type_amount_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monks', function (Blueprint $table) {
            $table->dropIndex('monks_status_type_index');
            $table->dropIndex('monks_status_pansa_name_index');
            $table->dropIndex('monks_status_name_index');
        });

        Schema::table('absences', function (Blueprint $table) {
            $table->dropIndex('absences_is_paid_absent_date_index');
            $table->dropIndex('absences_absent_date_index');
            $table->dropIndex('absences_monk_id_is_paid_index');
        });

        Schema::table('duty_schedules', function (Blueprint $table) {
            $table->dropIndex('duty_schedules_type_day_index');
            $table->dropIndex('duty_schedules_type_date_index');
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex('news_status_published_at_index');
            $table->dropIndex('news_category_status_published_index');
        });

        Schema::table('chants', function (Blueprint $table) {
            $table->dropIndex('chants_category_id_title_index');
            $table->dropIndex('chants_title_index');
        });

        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropIndex('hero_slides_status_sort_order_index');
        });

        Schema::table('construction_transactions', function (Blueprint $table) {
            $table->dropIndex('construction_tx_type_amount_index');
            $table->dropIndex('construction_tx_project_type_amount_index');
        });
    }
};
