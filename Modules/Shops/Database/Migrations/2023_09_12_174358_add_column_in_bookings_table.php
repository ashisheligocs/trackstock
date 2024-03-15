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
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices')->onUpdate('no action');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->double('tax_value')->nullable();
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign('bookings_invoice_id_foreign');
            $table->dropColumn('invoice_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('tax_value');
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropForeign('account_transactions_booking_id_foreign');
            $table->dropColumn('booking_id');
        });
    }
};
