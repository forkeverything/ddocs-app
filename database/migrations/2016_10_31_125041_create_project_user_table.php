<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_user', function (Blueprint $table) {
            $table->timestamps();
            $table->integer('project_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->primary(['project_id', 'user_id']);

            $table->boolean('accepted')->default(0);
            $table->boolean('admin')->default(0);
            $table->boolean('manager')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_user');
    }
}
