<?php

namespace App;

use App\Events\RecipientClaimedInvitation;
use App\Utilities\Traits\Hashable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use DB;
use Event;

/**
 * App\Checklist
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property string $description
 * @property integer $user_id
 * @property-read mixed $meta
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\FileRequest[] $requestedFiles
 * @property-read \App\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Recipient[] $recipients
 * @property-read mixed $hash
 * @method static \Illuminate\Database\Query\Builder|\App\Checklist whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Checklist whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Checklist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Checklist whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Checklist whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Checklist whereUserId($value)
 * @mixin \Eloquent
 */
class Checklist extends Model
{
    use Hashable;

    /**
     * Mass-assignable fields for a Checklist
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'user_id'
    ];

    protected $hidden = [
        'id'
    ];

    /**
     * Dynamic props to attach to each Model.
     *
     * @var array
     */
    protected $appends = [
        'meta',
        'hash'
    ];

    /**
     * Meta properties that tell us a little more about the Checklist.
     *
     * @return array
     */
    public function getMetaAttribute()
    {

        $meta = [];

        // Recipients
        $recipients = $this->fetchRecipients();
        $meta['num_recipients'] = $recipients->count();
        $meta['recipients'] = $recipients->pluck('email');

        // File Requests
        $files = $this->fetchFileRequests();
        $meta['num_total'] = $files->count();
        $meta['num_received'] = $files->where('status', 'received')->count();
        $meta['percentage_received'] = $meta['num_total'] ? round(100 * $meta['num_received'] / $meta['num_total'], 0) : 0;

        // Manually fetch using DB so our relation data isn't automatically appended to
        // our results and manually calling unset will break eager-loading. Separate
        // queries void messy joins and keeps our payload slim.

        return $meta;
    }

    /**
     * Recipient(s) that belong to this Checklist.
     *
     * @return Collection
     */
    protected function fetchRecipients()
    {
       return DB::table('recipients')
           ->where('checklist_id', '=', $this->id)
           ->get();
    }

    /**
     * FileRequest(s) that belong to this Checklist.
     *
     * @return Collection
     */
    protected function fetchFileRequests()
    {
        return DB::table('file_requests')
                         ->where('checklist_id', '=', $this->id)
                         ->get();
    }


    /**
     * A Checklist can have many files that it requires.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requestedFiles()
    {
        return $this->hasMany(FileRequest::class);
    }



    /**
     * Every Checklist was made by a single User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Checklist can have multiple Recipient(s).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipients()
    {
        return $this->hasMany(Recipient::class);
    }

    /**
     * Quick checker to see if Checklist was made by
     * the given User.
     *
     * @param User $user
     * @return bool
     */
    public function madeBy(User $user = null)
    {
        if(!$user) return false;
        return $this->user_id === $user->id;
    }

    /**
     * Quick update to turn off recipient notifications property.
     *
     * @return $this
     */
    public function turnOffRecipientNotifications()
    {
        $this->update([
            'recipient_notifications' => 0
        ]);
        return $this;
    }

    /**
     * Recipient (User) claim invite for this Checklist to get
     * free credits for recipient and user that made checklist.
     *
     * @param User $recipient
     * @return $this
     */
    public function claimInvite(User $recipient)
    {
        $this->update(['invitation_claimed' => 1]);
        $this->user->addCredits(10);
        // Give recipient 10 including default of 5.
        $recipient->addCredits(10);

        Event::fire(new RecipientClaimedInvitation($this, $recipient));

        return $this;
    }

    /**
     * Delete Checklist including file requests.
     *
     * @return bool|null
     */
    public function fullDelete()
    {
        foreach ($this->requestedFiles as $file) {
            $file->fullDelete();
        }
        return $this->delete();
    }

}
