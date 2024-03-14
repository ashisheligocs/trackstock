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
        Schema::create('ledgers_accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ledger_type')->unsigned()->index();
            $table->foreign('ledger_type')->references('id')->on('ledgers')->onDelete('cascade');
            $table->bigInteger('ledger_group')->unsigned()->index()->nullable();
            $table->foreign('ledger_group')->references('id')->on('ledgers_categories')->onDelete('cascade');
            $table->string("ledger_name")->nullable();
            $table->string("system_name")->nullable();
            $table->string("code")->nullable();
            $table->integer("is_bank")->default(0);
            $table->integer("show_in_day_book")->default(0);
            $table->integer("del_status")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ledgers_accounts');
    }
};
