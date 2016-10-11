<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Upload
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $file_name
 * @property string $path
 * @property integer $size
 * @property boolean $rejected
 * @property string $rejected_reason
 * @property integer $target_id
 * @property string $target_type
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $target
 * @method static \Illuminate\Database\Query\Builder|\App\Upload whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Upload whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Upload whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Upload whereFileName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Upload wherePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Upload whereSize($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Upload whereRejected($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Upload whereRejectedReason($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Upload whereTargetId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Upload whereTargetType($value)
 * @mixin \Eloquent
 */
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
