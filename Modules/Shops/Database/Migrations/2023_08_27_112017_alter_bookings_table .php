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
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('client_booking_status')->references('id')->on('booking_status')->onDelete('cascade');
            $table->integer("booking_status_main")->default('0')->comment('0=hold,1=cancel,2=booked,3=checkin,4=checkout,5=pending_payment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
