<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
