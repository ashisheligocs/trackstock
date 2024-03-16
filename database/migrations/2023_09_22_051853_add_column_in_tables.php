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
        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade')->onUpdate('no action');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('ledgers_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('quotations', function (Blueprint $table) {
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
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign('clients_shop_id_foreign');
            $table->dropColumn('shop_id');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign('suppliers_shop_id_foreign');
            $table->dropColumn('shop_id');
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropForeign('account_transactions_shop_id_foreign');
            $table->dropColumn('shop_id');
            $table->dropForeign('account_transactions_invoice_id_foreign');
            $table->dropColumn('invoice_id');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign('employees_shop_id_foreign');
            $table->dropColumn('shop_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign('expenses_shop_id_foreign');
            $table->dropColumn('shop_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign('invoices_shop_id_foreign');
            $table->dropColumn('shop_id');
        });

        Schema::table('ledgers_accounts', function (Blueprint $table) {
            $table->dropForeign('ledger_accounts_shop_id_foreign');
            $table->dropColumn('shop_id');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign('quotations_shop_id_foreign');
            $table->dropColumn('shop_id');
        });
    }
};
