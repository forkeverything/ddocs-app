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
        'path',
        'rejected',
        'rejected_reason',
        'file_request_id'
    ];

    protected $appends = [
        'file_name'
    ];

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
}
