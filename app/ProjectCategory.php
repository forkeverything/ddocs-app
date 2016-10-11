<?php

namespace App;

use App\Utilities\Traits\HasProjectItems;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ProjectCategory
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProjectCategory[] $categories
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProjectFile[] $files
 * @property-read mixed $items
 * @mixin \Eloquent
 */
class ProjectCategory extends Model
{
    use HasProjectItems;

    protected $fillable = [
        'name',
        'position',
        'project_id',
        'parent_type',
        'parent_id'
    ];

    protected $appends = [
        'items'
    ];

    protected $attributes = [
        'type' => 'App\\ProjectCategory'
    ];

    /**
     * Category can belong to a Project, Category or even a File.
     *
     * @return mixed
     */
    public function parent()
    {
        return $this->morphTo();
    }

    public function parentCategory()
    {
        return $this->parent()->where('type', 'App\\ProjectCategory')->first();
    }

    /**
     * Category can have nested categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function categories()
    {
        return $this->morphMany(ProjectCategory::class, 'parent');
    }

    /**
     * Files directly under this Category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function files()
    {
        return $this->morphMany(ProjectFile::class, 'parent');
    }

    /**
     * Grab items that belong directly to Category.
     *
     * @return mixed
     */
    public function getItemsAttribute()
    {
        return $this->items();
    }


}
