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
        Schema::table('ledgers_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('expense_category_id')->nullable();
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade')->onUpdate('no action');
        });
        Schema::table('ledgers_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('expense_sub_category_id')->nullable();
            $table->foreign('expense_sub_category_id')->references('id')->on('expense_sub_categories')->onDelete('cascade')->onUpdate('no action');
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('expense_transaction_id')->nullable();
            $table->foreign('expense_transaction_id')->references('id')->on('account_transactions')->onDelete('cascade')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ledgers_categories', function (Blueprint $table) {
            $table->dropForeign('ledgers_categories_expense_category_id_foreign');
            $table->dropColumn('expense_category_id');
        });
        Schema::table('ledgers_accounts', function (Blueprint $table) {
            $table->dropForeign('ledgers_accounts_expense_sub_category_id_foreign');
            $table->dropColumn('expense_sub_category_id');
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign('expenses_expense_transaction_id_foreign');
            $table->dropColumn('expense_transaction_id');
        });
    }
};
