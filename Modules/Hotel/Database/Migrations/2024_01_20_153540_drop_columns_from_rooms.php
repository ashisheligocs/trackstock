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
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('per_person');
            $table->dropColumn('extra_bed_capacity');
            $table->dropColumn('extra_person_capacity');
            $table->dropColumn('room_rate');
            $table->dropColumn('extra_bed_rate');
            $table->dropColumn('no_of_infant');
            $table->dropColumn('no_of_child');
            $table->dropColumn('no_of_person');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->integer('rate')->nullable();
            $table->integer('extra_bed_capacity')->nullable();
            $table->integer('extra_bed_rate')->nullable();
            $table->integer('extra_person_capacity')->nullable();
            $table->integer('per_person')->nullable();
            $table->integer('no_of_infant')->nullable();
            $table->integer('no_of_child')->nullable();
            $table->integer('no_of_person')->nullable();
        });
    }
};
