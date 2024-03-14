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
        Schema::create('ledgers_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('coa_id')->unsigned()->index();
            $table->foreign('coa_id')->references('id')->on('ledgers')->onDelete('cascade');
            $table->string("name");
            $table->string("system_name")->nullable();
            $table->string("parant_id")->nullable();
            $table->string("position")->nullable();
            $table->string("del_status")->default(0);
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
        Schema::dropIfExists('ledgers_categories');
    }
};
