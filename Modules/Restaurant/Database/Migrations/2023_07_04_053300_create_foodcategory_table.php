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
        Schema::create('foodcategorys', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->string('image');
            $table->integer("status")->default('0')->comment("0=inactive,1=active");
            $table->integer("del_status")->default('0');
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
        Schema::dropIfExists('foodcategorys');
    }
};
