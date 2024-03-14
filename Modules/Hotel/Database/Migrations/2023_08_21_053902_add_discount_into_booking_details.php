<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->double('discount')->after('meal_plan_persons')->nullable();
        });
    }

    public function down()
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
};
