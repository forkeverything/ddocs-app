<?php

namespace App;

use App\Utilities\Traits\HasUploads;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ProjectFile
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property string $description
 * @property integer $position
 * @property integer $project_folder_id
 * @property integer $file_request_id
 * @property-read \App\ProjectFolder $folder
 * @property-read \App\FileRequest $fileRequest
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Upload[] $uploads
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFile whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFile whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFile whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFile wherePosition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFile whereProjectFolderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFile whereFileRequestId($value)
 * @mixin \Eloquent
 * @property-read mixed $meta
 * @property-read mixed $attached
 */
class ProjectFile extends Model
{

    use HasUploads;

    /**
     * Assignable fields...
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'position',
        'description',
        'due',
        'weighting',
        'project_folder_id',
        'file_request_id'
    ];

    /**
     * Hidden attributes.
     *
     * @var array
     */
    protected $hidden = [
        'file_request_id'       // Since we're using the hash val to attach
    ];

    /**
     * Automatically appended dynamic attributes.
     *
     * @var array
     */
    protected $appends = [
        'meta'
    ];

    /**
     * Date attributes.
     *
     * @var array
     */
    protected $dates = [
        'due'
    ];

    /**
     * Dynamic meta properties.
     *
     * @return array
     */
    public function getMetaAttribute()
    {
        $meta = [];
        $uploads = $this->fetchUploads();
        $meta['num_uploads'] = $uploads->count();
        return $meta;
    }

    /**
     * Manual DB query to return file uploads.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function fetchUploads()
    {
        return $uploads = DB::table('uploads')
                     ->where('target_type', 'App\ProjectFile')
                     ->where('target_id', $this->id)
                     ->get();
    }

    /**
     * Eager-loads all the relevant relationships for a ProjectFile
     * @return $this
     */
    public function loadAllRelations()
    {
        return $this->load(
            'uploads',
            'fileRequest',
            'fileRequest.checklist.recipients'
        );
    }


    /**
     * The folder that the file is in.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function folder()
    {
        return $this->belongsTo(ProjectFolder::class, 'project_folder_id');
    }

    /**
     * Potentially linked File Request.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fileRequest()
    {
        return $this->belongsTo(FileRequest::class, 'file_request_id');
    }

    /**
     * A Project File could have lots of comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'subject');
    }

    /**
     * Could potentially have many uploads.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function uploads()
    {
        return $this->morphMany(Upload::class, 'target')->orderBy('created_at', 'desc');
    }
}
