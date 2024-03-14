<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->unsignedFloat('room_tax')->nullable()->after('booking_status');
            $table->unsignedFloat('meal_plan_tax')->nullable()->after('room_tax');
            $table->unsignedFloat('facility_tax')->nullable()->after('meal_plan_tax');
            $table->unsignedFloat('extra_bed_tax')->nullable()->after('facility_tax');
            $table->unsignedFloat('extra_person_tax')->nullable()->after('extra_bed_tax');
            $table->integer('meal_plan_persons')->after('extra_person_tax')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropColumn('room_tax');
            $table->dropColumn('meal_plan_tax');
            $table->dropColumn('facility_tax');
            $table->dropColumn('extra_bed_tax');
            $table->dropColumn('extra_person_tax');
            $table->dropColumn('meal_plan_persons');
        });
    }
};
