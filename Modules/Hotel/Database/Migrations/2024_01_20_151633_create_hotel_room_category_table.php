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
        Schema::create('hotel_room_category', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hotel_id')->unsigned()->index()->nullable();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
            $table->bigInteger('room_category')->unsigned()->index()->nullable();
            $table->foreign('room_category')->references('id')->on('room_categories')->onDelete('cascade');
            $table->integer('rate')->nullable();
            $table->integer('extra_bed_capacity')->nullable();
            $table->integer('extra_bed_rate')->nullable();
            $table->integer('extra_person_capacity')->nullable();
            $table->integer('per_person')->nullable();
            $table->integer('no_of_person')->nullable();
            $table->integer('no_of_child')->nullable();
            $table->integer('no_of_infant')->nullable();
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
        Schema::dropIfExists('hotel_room_category');
    }
};
