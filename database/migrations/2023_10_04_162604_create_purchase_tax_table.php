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
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign('purchases_tax_id_foreign');
            $table->dropColumn('tax_id');
        });
        Schema::create('purchase_tax', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('purchase_id')->unsigned()->index();
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
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
        Schema::dropIfExists('purchase_tax');
    }
};
