<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Project
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property string $description
 * @property integer $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProjectFolder[] $folders
 * @method static \Illuminate\Database\Query\Builder|\App\Project whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Project whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Project whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Project whereUserId($value)
 * @mixin \Eloquent
 */
class Project extends Model
{

    protected $fillable = [
        'name',
        'description',
        'user_id'
    ];

    /**
     * Project can have many different folders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function folders()
    {
        return $this->hasMany(ProjectFolder::class, 'project_id');
    }
}
