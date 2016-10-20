<?php

namespace App;

use App\Utilities\Traits\Hashable;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Note
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $position
 * @property boolean $checked
 * @property string $body
 * @property integer $file_request_id
 * @property-read \App\FileRequest $fileRequest
 * @property-read mixed $hash
 * @method static \Illuminate\Database\Query\Builder|\App\Note whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Note whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Note whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Note wherePosition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Note whereChecked($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Note whereBody($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Note whereFileRequestId($value)
 * @mixin \Eloquent
 */
class Note extends Model
{

    use Hashable;

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
}
