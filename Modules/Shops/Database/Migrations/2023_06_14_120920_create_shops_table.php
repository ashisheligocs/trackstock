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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name');
            $table->longText('shop_address');
            $table->string('shop_phone')->unique();
            $table->string('shop_phone1')->nullable();
            $table->string('shop_email')->unique();
            $table->string('shop_prefix')->nullable();
            $table->integer("shop_Status")->default('0')->comment('0=active,1=inactive');
            $table->string('contact_phone')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('images')->nullable();
            $table->text('state')->nullable();
            $table->text('city')->nullable();
            $table->text('image_path')->nullable();
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
        Schema::dropIfExists('shops');
    }
};
