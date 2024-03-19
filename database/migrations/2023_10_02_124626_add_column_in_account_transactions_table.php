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
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('item_restro_orders')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('non_invoice_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('non_purchase_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropForeign('account_transactions_order_id_foreign');
            $table->dropColumn('order_id');
        });
        
        Schema::table('non_invoice_payments', function (Blueprint $table) {
            $table->dropForeign('non_invoice_payments_shop_id_foreign');
            $table->dropColumn('shop_id');
        });

        Schema::table('non_purchase_payments', function (Blueprint $table) {
            $table->dropForeign('non_purchase_payments_shop_id_foreign');
            $table->dropColumn('shop_id');
        });
    }
};
