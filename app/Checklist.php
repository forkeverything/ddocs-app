<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    /**
     * Mass-assignable fields for a Checklist
     *
     * @var array
     */
    protected $fillable = [
        'recipient',
        'name',
        'description',
        'user_id',
        'recipient_notifications'
    ];

    /**
     * Dynamic props to attach to each Model.
     *
     * @var array
     */
    protected $appends = [
        'hash',
        'progress'
    ];


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
     * Get the hash'd id of this model.
     *
     * @return mixed
     */
    public function getHashAttribute()
    {
        return hashId($this);
    }

    /**
     * Calculate the percentage completion of Checklist using
     * the number of File(s) received.
     *
     * @return float
     */
    public function getProgressAttribute()
    {
        $files = $this->requestedFiles;
        // Count only required files that are received....
        $received = $files->where('required', 1)->where('status', 'received')->count();
        $total = $files->count();
        if(! $total) return;
        return round(100 * $received / $total, 0);
    }

    /**
     * Quick checker to see if Checklist was made by
     * the given User.
     *
     * @param User $user
     * @return bool
     */
    public function madeBy(User $user)
    {
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
}
