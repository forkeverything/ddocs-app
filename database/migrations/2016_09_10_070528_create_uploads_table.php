<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('file_name');
            $table->string('path');
            $table->integer('size');

            $table->boolean('rejected')->default(0);
            $table->text('rejected_reason')->nullable();

            $table->integer('target_id')->unsigned();
            $table->string('target_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('uploads');
    }
}
