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
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('images')->nullable();
            $table->text('state')->nullable();
            $table->text('city')->nullable();
            $table->text('image_path')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('images');
            $table->dropColumn('state');
            $table->dropColumn('city');
            $table->dropColumn('image_path');
        });
    }
};
