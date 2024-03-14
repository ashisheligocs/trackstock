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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number');
            $table->bigInteger('customer_id')->unsigned()->nullable()->index();
            $table->foreign('customer_id')->references('id')->on('clients')->onDelete('cascade');
            $table->integer('total_room');
            $table->integer('advance_amount');
            $table->string('advance_remarks')->nullable();
            $table->integer('paid_amount');
            $table->integer('total_price');
            $table->bigInteger('payment_method')->unsigned()->nullable()->index();
            $table->string('remarks')->nullable();
            $table->integer('discount_type')->nullable();
            $table->integer('discount_amount')->nullable();
            $table->string('discount_reason')->nullable();
            $table->string('full_guest_name')->nullable();
            $table->string('special_request')->nullable();
            $table->string('comments')->nullable();
            $table->dateTime('booking_date');
            $table->dateTime('check_in_date');
            $table->dateTime('check_out_date');
            $table->bigInteger('hotel_id')->unsigned()->index();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
            // $table->integer("booking_status")->default('0')->comment('0=pending,1=cancel,2=booked,3=checkin,4=checkout');
            $table->bigInteger("client_booking_status")->default('0')->comment('1=pending,=hold,3=confirmed,4=available')->unsigned()->index();
            $table->integer("booked_from")->default('0')->comment('0=admin,1=user,3=agents');
            $table->bigInteger('booking_source')->unsigned()->index()->nullable()->nullable();
            $table->foreign('booking_source')->references('id')->on('clients')->onDelete('cascade');
            $table->string('arrival_from')->nullable();
            $table->string('purpose')->nullable();
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
        Schema::dropIfExists('bookings');
    }
};
