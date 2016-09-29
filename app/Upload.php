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
        'target_id',
        'target_type'
    ];

    /**
     * An Upload could be for a FileRequest or ProjectFile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function target()
    {
        return $this->morphTo();
    }
}
