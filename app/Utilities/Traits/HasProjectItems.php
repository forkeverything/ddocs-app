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
        return getProjectItems($type, $this->id);
    }

    /**
     * Recursively deletes all sub-items for this Model.
     *
     */
    public function deleteAllIncludingChildren()
    {
        foreach ($this->items as $item) {
            $itemModel = $this->hydrateItem($item);
            $itemModel->deleteAllIncludingChildren();
        }
        $this->delete();
    }

    /**
     * Turns Item into their Model.
     *
     * @param $item
     * @return mixed
     */
    public function hydrateItem($item)
    {
        return call_user_func($item->type . '::find', $item->id);
    }
}