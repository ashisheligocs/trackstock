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
        Schema::table('invoice_return_products', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable()->after('product_id');
            $table->foreign('service_id')->references('id')->on('other_services')->onDelete('cascade')->onUpdate('no action');    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_return_products', function (Blueprint $table) {
            $table->dropForeign('invoice_return_products_service_id_foreign');
            $table->dropColumn('service_id');
        });
    }
};
