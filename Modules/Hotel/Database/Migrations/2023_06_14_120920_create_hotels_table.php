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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('hotel_name');
            $table->longText('hotel_address');
            $table->bigInteger('hotelcategory_id')->unsigned()->index();
            $table->foreign('hotelcategory_id')->references('id')->on('hotelcategories')->onDelete('cascade');
            $table->string('hotel_phone')->unique();
            $table->string('hotel_phone1')->nullable();
            $table->string('hotel_email')->unique();
            $table->integer('total_no_of_rooms')->nullable();
            $table->integer("hotel_Status")->default('0')->comment('0=active,1=inactive');;
            $table->integer('no_of_floor')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_name')->nullable();
            // $table->longText('hotel_facility_ids');
            $table->integer("del_status")->default('0')->comment('0=active,1=delete');
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
        Schema::dropIfExists('hotels');
    }
};
