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
        Schema::table('non_invoice_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->after('client_id')->nullable();
            $table->foreign('order_id')->references('id')->on('item_restro_orders')->onDelete('cascade')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('non_invoice_payments', function (Blueprint $table) {
            $table->dropForeign('non_invoice_payments_order_id_foreign');
            $table->dropColumn('order_id');
        });
    }
};
