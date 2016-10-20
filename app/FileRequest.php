<?php

namespace App;

use App\Utilities\Traits\Hashable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\FileRequest
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $due
 * @property string $version
 * @property string $status
 * @property integer $checklist_id
 * @property integer $file_id
 * @property-read mixed $name
 * @property-read mixed $latest_upload
 * @property-read mixed $hash
 * @property-read \App\Checklist $checklist
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Upload[] $uploads
 * @property-read \App\File $file
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @method static \Illuminate\Database\Query\Builder|\App\FileRequest whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FileRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FileRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FileRequest whereDue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FileRequest whereVersion($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FileRequest whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FileRequest whereChecklistId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FileRequest whereFileId($value)
 * @mixin \Eloquent
 */
class FileRequest extends Model
{
    use Hashable;

    /**
     * Hidden attributes.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'checklist_id'
    ];

    /**
     * Assignable fields.
     *
     * @var array
     */
    protected $fillable = [
        'due',
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
        'latest_upload',
        'checklist_hash'
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

    /**
     * The last uploaded file.
     *
     * @return mixed
     */
    public function getLatestUploadAttribute()
    {
        return $this->uploads()->orderBy('created_at', 'desc')->get()->first();
    }

    /**
     * The hash'd checklist id.
     *
     * @return mixed
     */
    public function getChecklistHashAttribute()
    {
        return hashId('checklist', $this->checklist_id);
    }


    /**
     * Format as Carbon Date only if value given to prevent '0000-00-00 00:00:00'
     *
     * @param $value
     */
    public function setDueAttribute($value)
    {
        if ($value) {
            $this->attributes['due'] = Carbon::createFromFormat('d/m/Y', $value);
        } else {
            $this->attributes['due'] = null;
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
        return $this->morphMany(Upload::class, 'target')->orderBy('created_at', 'asc');
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
        if (!in_array($status, $allowedStatuses)) abort(500, "Can't whether a File has an invalid status: " . $status);
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
        if (!$this->hasStatus('received')) abort(403, "Can't reject a file we haven't received or already marked rejected.");

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
