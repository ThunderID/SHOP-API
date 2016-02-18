<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductExtensionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_extensions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('upc')->unique();
            $table->string('name');
            $table->double('price');
            $table->boolean('is_active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['deleted_at', 'upc', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('product_extensions');
    }
}
