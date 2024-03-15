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
        Schema::create('facility_taxs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('facility_id')->unsigned()->index();
            $table->foreign('facility_id')->references('id')->on('room_facilitytdatas')->onDelete('cascade');
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
        Schema::dropIfExists('facility_taxs');
    }
};
