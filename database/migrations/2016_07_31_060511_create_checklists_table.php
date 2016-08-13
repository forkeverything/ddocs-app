<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklists', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('recipient');
            $table->string('name');
            $table->text('description')->nullable();

            // Whether recipient receives email notifications
            $table->boolean('recipient_notifications')->default(1);
            // Has the invitation offer been claimed by recipient?
            $table->boolean('invitation_claimed')->default(0);

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('checklists');
    }
}
