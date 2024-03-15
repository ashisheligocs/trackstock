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
        Schema::table('booking_complementrays', function (Blueprint $table) {
            $table->integer('quantity')->nullable()->after('complementary_id');
            $table->integer('modified_rate')->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_complementrays', function (Blueprint $table) {
            $table->dropColumn('quantity');
            $table->dropColumn('modified_rate');
        });
    }
};
