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
        Schema::table('room_category_tax', function (Blueprint $table) {
            $table->renameColumn('room_id', 'hotel_room_category_id');
            $table->foreign('hotel_room_category_id')->references('id')->on('hotel_room_category')->onDelete('cascade');
            $table->foreign('tax_id')->references('id')->on('vat_rates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('room_category_tax', function (Blueprint $table) {
            $table->renameColumn('hotel_room_category_id', 'room_id');
            $table->dropForeign('room_category_tax_hotel_room_category_id_foreign');
            $table->dropForeign('room_category_tax_tax_id_foreign');
        });
    }
};
