<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    /**
     * Assignable fields.
     *
     * @var array
     */
    protected $fillable = [
        'file_name',
        'path',
        'size',
        'rejected',
        'rejected_reason',
        'file_request_id'
    ];

    /**
     * An Upload is for a specific File Request.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fileRequest()
    {
        return $this->belongsTo(FileRequest::class, 'file_request_id');
    }
}
