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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_name')->nullable();
            $table->bigInteger('room_categorary')->unsigned()->index()->nullable();
            $table->foreign('room_categorary')->references('id')->on('room_categories')->onDelete('cascade');
            $table->bigInteger('hotel_id')->unsigned()->index()->nullable();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
            $table->bigInteger('bed_type_id')->unsigned()->index()->nullable();
            $table->foreign('bed_type_id')->references('id')->on('beds')->onDelete('cascade');
            $table->longText('roomdescription')->nullable();
            $table->integer('no_of_person')->nullable();
            $table->integer('extra_bed_capacity')->nullable();
            $table->integer('no_of_child')->nullable();
            $table->integer('room_rate')->nullable();
            $table->bigInteger('floor_id')->unsigned()->index()->nullable();
            $table->foreign('floor_id')->references('id')->on('floors')->onDelete('cascade');
            $table->integer('extra_bed_rate')->nullable();
            $table->integer('per_person')->nullable();
            // $table->integer('floor_id')->nullable();
            $table->integer('no_of_infant')->nullable();
            $table->integer("del_status")->default('0');
            $table->integer("room_status")->default('0');
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
        Schema::dropIfExists('rooms');
    }
};
