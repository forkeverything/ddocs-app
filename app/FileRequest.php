<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FileRequest extends Model
{
    /**
     * Assignable fields.
     *
     * @var array
     */
    protected $fillable = [
        'due',
        'weighting',
        'version',
        'status',
        'checklist_id',
        'file_id'
    ];

    /**
     * Date fields to be converted automatically via Carbon.
     *
     * @var array
     */
    protected $dates = [
        'due'
    ];

    /**
     * Dynamic attributes we want to attach to each model.
     *
     * @var array
     */
    protected $appends = [
        'name',
        'hash',
        'latest_upload'
    ];

    /**
     * Get the name from the File.
     *
     * @return mixed
     */
    public function getNameAttribute()
    {
        return $this->file->name;
    }

    public function getLatestUploadAttribute()
    {
        return Upload::where('file_request_id', $this->id)->orderBy('created_at', 'desc')->first();
    }

    /**
     * Get the hash'd id of this model.
     *
     * @return mixed
     */
    public function getHashAttribute()
    {
        return hashId('file-request',  $this);
    }

    /**
     * Format as Carbon Date only if value given to prevent '0000-00-00 00:00:00'
     *
     * @param $value
     */
    public function setDueAttribute($value)
    {
        if($value) {
            $this->attributes['due'] = Carbon::createFromFormat('d/m/Y', $value);
        } else {
            $this->attributes['due'] = null;
        }
    }

    /**
     * Mutator for weighting property.
     *
     * @param $value
     */
    public function setWeightingAttribute($value)
    {
        if($value) {
            $this->attributes['weighting'] = $value;
        } else {
            $this->attributes['weighting'] = null;
        }
    }

    /**
     * All File(s) belong to a single Checklist that has required them.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    /**
     * A File Request could potentially have many uploads.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function uploads()
    {
        return $this->hasMany(Upload::class)->orderBy('created_at', 'asc');
    }

    /**
     * FileRequest belongs to a File.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file()
    {
        return $this->belongsTo(File::class);
    }

    /**
     * FileRequest could potentially ahve many Note(s) attached
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Helper func to see if File's status attribute matches given
     * param.
     *
     * @param $status
     * @return bool
     */
    public function hasStatus($status)
    {
        $allowedStatuses = ['waiting', 'received', 'rejected'];
        if(! in_array($status, $allowedStatuses)) abort(500, "Can't whether a File has an invalid status: " . $status);
        return $this->status === $status;
    }

    /**
     * Reject the latest upload for this File Request.
     *
     * @param $reason
     * @return $this
     */
    public function reject($reason)
    {
        if(! $this->hasStatus('received')) abort(403, "Can't reject a file we haven't received or already marked rejected.");

        // Mark this request as rejected and we'll also increment version
        $this->update([
            'status' => 'rejected',
            'version' => $this->version + 1
        ]);

        // Update the upload as well as store the reason
        $this->uploads->last()->update([
            'rejected' => 1,
            'rejected_reason' => $reason
        ]);

        return $this;
    }

    /**
     * A File Request could have lots of comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'subject');
    }
}
