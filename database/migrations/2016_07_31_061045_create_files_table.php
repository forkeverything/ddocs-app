<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('name');
            $table->string('path')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('due')->nullable();

            $table->boolean('required')->default(1);

            $table->string('status')->default('waiting');       // 'waiting', 'received', 'rejected'

            $table->integer('version')->default(1);

            $table->integer('checklist_id')->unsigned();
            $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('files');
    }
}
