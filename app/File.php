<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\File
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property string $description
 * @property integer $user_id
 * @property-read mixed $file_name
 * @property-read \App\FileRequest $fileRequest
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Query\Builder|\App\File whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereUserId($value)
 * @mixin \Eloquent
 */
class File extends Model
{


    /**
     * Assignable fields.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'user_id'
    ];


//    protected $appends = [
//        'file_name'
//    ];

    /**
     * File name as parsed out from path.
     *
     * @return mixed
     */
    public function getFileNameAttribute()
    {
        preg_match("/[^\/]+$/", $this->path, $name);
        return $name[0];
    }

    /**
     * Each Physical File only belongs to a single File Request.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fileRequest()
    {
        return $this->belongsTo(FileRequest::class);
    }

    /**
     * Each File is specific to a User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
