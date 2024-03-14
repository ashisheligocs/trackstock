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
        Schema::create('invoice_services_tax', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id')->nullable(); 
            $table->integer('tax_id' )->nullable(); 
            $table->text('name' )->nullable(); 
            $table->text('rate' )->nullable(); 
            $table->double('amount', 12, 2)->nullable(); 
            $table->text('code' )->nullable(); 
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
        Schema::dropIfExists('invoice_services_tax');
    }
};
