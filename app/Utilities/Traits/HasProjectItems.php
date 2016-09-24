<?php


namespace App\Utilities\Traits;


use Illuminate\Support\Facades\DB;
use ReflectionClass;

trait HasProjectItems
{
    /**
     * Grab items that belong directly to Project.
     *
     * @return mixed
     */
    public function items()
    {
        $type = get_class($this);
        return getProjectItemsWithNested($type, $this->id);
    }
}