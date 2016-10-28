<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ProjectFolder
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property integer $position
 * @property integer $project_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProjectFile[] $files
 * @property-read \App\Project $project
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFolder whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFolder whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFolder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFolder whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFolder wherePosition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProjectFolder whereProjectId($value)
 * @mixin \Eloquent
 */
class ProjectFolder extends Model
{

    protected $fillable = [
        'name',
        'project_id',
        'position'
    ];

    /**
     * Folder can contain many files
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(ProjectFile::class, 'project_folder_id');
    }

    /**
     * Folders all live within a single Project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Recursively delete each ProjectFile within folder before deleting
     * the folder.
     *
     * @return bool|null
     */
    public function fullDelete()
    {
        foreach ($this->files as $file) {
            $file->fullDelete();
        }
        $this->delete();
        return response("Deleted project folder");
    }
}
