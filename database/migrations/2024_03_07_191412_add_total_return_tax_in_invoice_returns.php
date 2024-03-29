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
        Schema::table('invoice_returns', function (Blueprint $table) {
            $table->double('total_return_tax', 12, 2)->nullable()->after('total_return');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_returns', function (Blueprint $table) {
            $table->dropColumn('total_return_tax');
        });
    }
};
