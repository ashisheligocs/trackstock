<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_tax_slab', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vat_rate_id');
            $table->unsignedBigInteger('tax_slab_id');

            $table->timestamps();

            $table->foreign('vat_rate_id')->references('id')->on('vat_rates')->onDelete('cascade')->onUpdate('no action');
            $table->foreign('tax_slab_id')->references('id')->on('tax_slabs')->onDelete('cascade')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tax_tax_slab');
    }
};
