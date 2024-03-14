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
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade')->onUpdate('no action');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('ledgers_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade')->onUpdate('no action');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade')->onUpdate('no action');
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
            $table->dropForeign('clients_hotel_id_foreign');
            $table->dropColumn('hotel_id');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign('suppliers_hotel_id_foreign');
            $table->dropColumn('hotel_id');
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropForeign('account_transactions_hotel_id_foreign');
            $table->dropColumn('hotel_id');
            $table->dropForeign('account_transactions_invoice_id_foreign');
            $table->dropColumn('invoice_id');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign('employees_hotel_id_foreign');
            $table->dropColumn('hotel_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign('expenses_hotel_id_foreign');
            $table->dropColumn('hotel_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign('invoices_hotel_id_foreign');
            $table->dropColumn('hotel_id');
        });

        Schema::table('ledgers_accounts', function (Blueprint $table) {
            $table->dropForeign('ledger_accounts_hotel_id_foreign');
            $table->dropColumn('hotel_id');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign('quotations_hotel_id_foreign');
            $table->dropColumn('hotel_id');
        });
    }
};
