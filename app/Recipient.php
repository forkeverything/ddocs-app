<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

class Recipient extends Model
{
    protected $fillable = [
        'email',
        'receive_notifications',
        'invitation_claimed',
        'checklist_id'
    ];

    /**
     * Recipient belongs to a single Checklist.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    /**
     * Turns off notifications for this Recipient.
     *
     * @return Boolean
     */
    public function turnOffNotifications()
    {
        return $this->update([
            'receive_notifications' => 0
        ]);
    }
}
