<?php

namespace App;

use App\Utilities\Traits\HasProjectItems;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    use HasProjectItems;

    protected $fillable = [
        'name',
        'description',
        'user_id'
    ];

    /**
     * Project can have many Categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function categories()
    {
        return $this->morphMany(ProjectCategory::class, 'parent');
    }

    /**
     * Direct File(s) that aren't nested.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function files()
    {
        return $this->morphMany(ProjectFile::class, 'parent');
    }

    /**
     * Retrieve all the File(s) even the nested ones.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allFiles()
    {
        return $this->hasMany(ProjectFile::class, 'project_id');
    }

    /**
     * Append items property to Project Category.
     *
     * @return $this
     */
    public function withItems()
    {
        $this->setAttribute('items', $this->items());
        return $this;
    }
}
