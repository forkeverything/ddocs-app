<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectFileUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_file_uploads', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('file_name');
            $table->string('path');
            $table->integer('size');

            $table->integer('project_file_id')->unsigned();
            $table->foreign('project_file_id')->references('id')->on('project_files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('project_file_uploads');
    }
}
