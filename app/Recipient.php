<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
