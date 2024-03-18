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
        Schema::create('item_restro_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id_uniq');
            $table->bigInteger('invoice_id')->nullable()->unsigned();
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('no action');
            $table->bigInteger('customer_id')->nullable()->unsigned()->index();
            $table->foreign('customer_id')->references('id')->on('clients')->onDelete('cascade');
            $table->dateTime('order_date');
            $table->integer("order_status")->default('0')->comment("0=pending,1=completed,2=cancel");
            $table->integer("payment_status")->default('0')->comment("0=pending,1=completed,3=merged");
            $table->string("total_amount");
            $table->string("discount");
            $table->string("tax");
            $table->bigInteger('shop_id')->unsigned()->index()->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
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
        Schema::dropIfExists('item_restro_orders');
    }
};
