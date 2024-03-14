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
        Schema::table('room_tax', function (Blueprint $table) {
            $table->dropForeign('room_tax_room_id_foreign');
            $table->dropForeign('room_tax_tax_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('room_tax', function (Blueprint $table) {
            $table->bigInteger('room_id')->unsigned()->index()->nullable();
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');

            $table->bigInteger('tax_id')->unsigned()->index()->nullable();
            $table->foreign('tax_id')->references('id')->on('vat_rates')->onDelete('cascade');
        });
    }
};
