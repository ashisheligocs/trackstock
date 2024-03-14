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
            $table->unsignedBigInteger('payroll_id')->nullable();
            $table->foreign('payroll_id')->references('id')->on('payrolls')->onDelete('cascade')->onUpdate('no action');
        });
        Schema::table('booking_details', function (Blueprint $table) {
            $table->float('modified_room_rate')->nullable();
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('tax_included')->nullable();
        });
        Schema::table('balance_tansfers', function (Blueprint $table) {
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
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropForeign('account_transactions_payroll_id_foreign');
            $table->dropColumn('payroll_id');
        });
        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropColumn('modified_room_rate');
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('tax_included');
        });
        Schema::table('balance_tansfers', function (Blueprint $table) {
            $table->dropForeign('balance_tansfers_hotel_id_foreign');
            $table->dropColumn('hotel_id');
        });
    }
};
