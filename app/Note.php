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

    protected  $hidden = [
        'id'
    ];

    protected $appends = [
        'hash'
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

    /**
     * Get the hash'd id of this model.
     *
     * @return mixed
     */
    public function getHashAttribute()
    {
        return hashId('note',  $this);
    }
}
