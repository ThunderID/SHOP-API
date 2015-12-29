<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatUserViewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stat_user_views', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('statable_id')->unsigned()->index();
            $table->string('statable_type', 255);
            $table->integer('view');
            $table->datetime('ondate');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['deleted_at', 'ondate', 'statable_id']);
            $table->index(['deleted_at', 'ondate', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('stat_user_views');
    }
}
