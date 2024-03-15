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
        Schema::create('room_facilitytdatas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hotel_id')->unsigned()->index();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
            // $table->bigInteger('room_cat_id')->unsigned()->index();
            // $table->foreign('room_cat_id')->references('id')->on('room_categories')->onDelete('cascade');
            $table->bigInteger('facilityId')->unsigned()->index();
            $table->foreign('facilityId')->references('id')->on('room_facility')->onDelete('cascade');
            $table->integer("price");
            $table->integer("status")->default(0);
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
        Schema::dropIfExists('room_facilitytdatas');
    }
};
