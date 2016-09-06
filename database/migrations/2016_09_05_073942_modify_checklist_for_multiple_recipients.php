<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyChecklistForMultipleRecipients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $checklists = \App\Checklist::all();

        foreach ($checklists as $checklist) {

            // Migrate recipient to own table
            if($checklist->recipient) {
                \App\Recipient::create([
                    'email' => $checklist->recipient,
                    'receive_notifications' => $checklist->recipient_notifications,
                    'invitation_claimed' => $checklist->invitation_claimed,
                    'checklist_id' => $checklist->id
                ]);
            }

        }

        Schema::table('checklists', function (Blueprint $table) {
            $table->dropColumn(['recipient', 'recipient_notifications', 'invitation_claimed']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checklists', function (Blueprint $table) {
            $table->string('recipient')->nullable();
            // Whether recipient receives email notifications
            $table->boolean('recipient_notifications')->default(1);
            // Has the invitation offer been claimed by recipient?
            $table->boolean('invitation_claimed')->default(0);
        });
    }
}
