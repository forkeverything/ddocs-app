<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

}
