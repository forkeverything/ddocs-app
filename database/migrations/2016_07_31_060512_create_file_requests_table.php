<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('name');
            $table->text('description')->nullable();

            $table->dateTime('due')->nullable();
            $table->string('version')->default(1);

            $table->string('status')->default('waiting');       // 'waiting', 'received', 'rejected'

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
        Schema::drop('file_requests');
    }
}
