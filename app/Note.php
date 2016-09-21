<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    /**
     * Fillable fields.
     *
     * @var array
     */
    protected $fillable = [
        'position',
        'body',
        'checked',
        'file_request_id'
    ];

    /**
     * Notes are attached to a FileRequest.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fileRequest()
    {
        return $this->belongsTo(FileRequest::class);
    }
}
