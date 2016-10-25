<?php

namespace App;

use App\Utilities\Traits\Hashable;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * App\Recipient
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $email
 * @property boolean $receive_notifications
 * @property boolean $invitation_claimed
 * @property integer $checklist_id
 * @property-read \App\Checklist $checklist
 * @method static \Illuminate\Database\Query\Builder|\App\Recipient whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Recipient whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Recipient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Recipient whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Recipient whereReceiveNotifications($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Recipient whereInvitationClaimed($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Recipient whereChecklistId($value)
 * @mixin \Eloquent
 * @property-read mixed $hash
 */
class Recipient extends Model
{
    use Hashable;

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
