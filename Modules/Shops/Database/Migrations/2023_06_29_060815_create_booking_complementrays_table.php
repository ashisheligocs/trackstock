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
        Schema::create('booking_complementrays', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('booking_Detail_id')->unsigned()->index();
            $table->foreign('booking_Detail_id')->references('id')->on('booking_details')->onDelete('cascade');
            $table->bigInteger('complementary_id')->unsigned()->index();
            $table->foreign('complementary_id')->references('id')->on('room_facility')->onDelete('cascade');
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
        Schema::dropIfExists('booking_complementrays');
    }
};
