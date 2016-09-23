<?php

namespace App;

use App\Utilities\Traits\HasProjectItems;
use Illuminate\Database\Eloquent\Model;

class ProjectFile extends Model
{
    use HasProjectItems;

    protected $fillable = [
        'name',
        'position',
        'description',
        'weighting',
        'project_id',
        'file_request_id',
        'parent_type',
        'parent_id'
    ];

    protected $appends = [
        'items'
    ];

    protected $attributes = [
        'type' => 'App\\ProjectFile'
    ];

    /**
     * Set which Project this File belongs to. It will save us from hunting
     * through nested relationships when we just want all the files.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The parent of the file: Project, Categeory or even a parent File.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function parent()
    {
        return $this->morphTo();
    }

    /**
     * Project Files can have nested Categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function categories()
    {
        return $this->morphMany(ProjectCategory::class, 'parent');
    }

    /**
     * Files directly under this File.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function files()
    {
        return $this->morphMany(ProjectFile::class, 'parent');
    }

    /**
     * Grab items that belong directly.
     *
     * @return mixed
     */
    public function getItemsAttribute()
    {
        return $this->items();
    }
}
