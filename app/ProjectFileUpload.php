<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ProjectFileUpload
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $file_name
 * @property string $path
 * @property integer $size
 * @property integer $project_file_id
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFileUpload whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFileUpload whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFileUpload whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFileUpload whereFileName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFileUpload wherePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFileUpload whereSize($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFileUpload whereProjectFileId($value)
 * @mixin \Eloquent
 */
class ProjectFileUpload extends Model
{
    //
}
