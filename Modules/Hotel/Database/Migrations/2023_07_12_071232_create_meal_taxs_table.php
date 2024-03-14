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
        Schema::create('meal_taxs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('meal_price_id')->unsigned()->index();
            $table->foreign('meal_price_id')->references('id')->on('hotel_meal_plans')->onDelete('cascade');
            $table->bigInteger('tax_id')->unsigned()->index();
            $table->foreign('tax_id')->references('id')->on('vat_rates')->onDelete('cascade');
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
        Schema::dropIfExists('meal_taxs');
    }
};
