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
            $table->json('final_gst_rates')->nullable()->after('purpose');
            $table->integer('extra_charge')->nullable()->after('final_gst_rates');
            $table->mediumText('extra_charge_comment')->nullable()->after('extra_charge');
            $table->integer('special_discount_type')->nullable()->after('discount_reason');
            $table->integer('special_discount_amount')->nullable()->after('special_discount_type');
            $table->integer('special_discount_rate')->nullable()->after('special_discount_amount');
            $table->integer('credit_amount')->nullable()->after('extra_charge_comment');
            $table->integer('credit_payment_method')->nullable()->after('credit_amount');
            $table->integer('remaining_amount')->nullable()->after('credit_payment_method');
            $table->unsignedBigInteger('booking_type_id')->nullable();
            $table->foreign('booking_type_id')->references('id')->on('booking_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('final_gst_rates');
            $table->dropColumn('extra_charge');
            $table->dropColumn('extra_charge_comment');
            $table->dropColumn('special_discount_type');
            $table->dropColumn('special_discount_amount');
            $table->dropColumn('special_discount_rate');
            $table->dropColumn('credit_amount');
            $table->dropColumn('credit_payment_method');
            $table->dropColumn('remaining_amount');
        });
    }
};
