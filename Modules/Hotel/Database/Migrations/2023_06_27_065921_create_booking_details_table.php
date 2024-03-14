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
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('booking_id')->unsigned()->index();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->bigInteger('room_id')->unsigned()->index();
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->bigInteger('meal_plan_id')->unsigned()->index()->nullable();
            $table->foreign('meal_plan_id')->references('id')->on('hotel_meal_plans')->onDelete('cascade');
            $table->integer('adult')->nullable();
            $table->integer('infant')->nullable();
            $table->integer('children')->nullable();
            $table->integer('extra_bed')->nullable();
            $table->integer('extra_person')->nullable();
            $table->integer('extra_child')->nullable();
            $table->integer('extra_facility_days')->nullable();
            $table->string('room_no');
            $table->integer('room_rate');
            $table->integer("booking_status")->default('0')->comment('0=hold,1=cancel,2=booked,3=checkin,4=checkout,5=pending_payment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_details');
    }
};
