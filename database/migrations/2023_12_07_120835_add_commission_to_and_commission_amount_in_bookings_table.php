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
        Schema::table('bookings', function (Blueprint $table) {
            $table->bigInteger('commission_to')->unsigned()->index()->nullable();
            $table->foreign('commission_to')->references('id')->on('clients')->onDelete('cascade')->onUpdate('no action');
            $table->integer('commission_amount')->default(0)->after('commission_to');
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
            $table->dropForeign('bookings_commission_to_id_foreign');
            $table->dropColumn('commission_to');
            $table->dropColumn('commission_amount');
        });
    }
};
