<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_files', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('name');
            $table->integer('position');

            $table->text('description')->nullable();
            $table->decimal('weighting', 6, 2)->nullable();

            $table->string('type');

            $table->integer('file_request_id')->unsigned()->nullable();
            $table->foreign('file_request_id')->references('id')->on('file_requests')->onDelete('set null');

            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->string('parent_type');
            $table->integer('parent_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('project_files');
    }
}
