<?php

namespace App;

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
 */
class ProjectFile extends Model
{

    protected $fillable = [
        'name',
        'position',
        'description',
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
     * Always eager-loaded relations.
     *
     * @var array
     */
    protected $with = [
        'fileRequest'       // If one's attached we'll want to include it
    ];

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
        return $this->morphMany(Upload::class, 'target')->orderBy('created_at', 'asc');
    }

}
