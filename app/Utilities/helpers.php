<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Hash eloquent model id
 * @param $connection
 * @param Model $model
 * @return mixed
 */
function hashId($connection, $subject)
{
    $id = $subject;
    if($subject instanceof Model) $id = $subject->id;
    return Hashids::connection($connection)->encode($id);
}

/**
 * Takes a hashed Id and decodes it to return an Eloquent Model Id
 *
 * @param $hash
 * @return mixed
 */
function unhashId($connection, $hash)
{
    return array_first(Hashids::connection($connection)->decode($hash));
}

/**
 * Returns the AWS URL using our credentials in .env
 *
 * @return string
 */
function awsURL()
{
    return 'https://s3-' . env('AWS_REGION') . '.amazonaws.com/' . env('AWS_BUCKET') .  '/';
}

/**
 * Returns unique values for an array. Unlike array_unique(), this is
 * case insensitive.
 *
 * @param $array
 * @return array
 */
function array_iunique($array) {
    return array_intersect_key(
        $array,
        array_unique(array_map("StrToLower",$array))
    );
}

/**
 * Same as array_unique() but it will check multi-dimensional arrays.
 *
 * @param $array
 * @return array
 */
function array_unique_nested($array)
{
    return array_map("unserialize", array_unique(array_map("serialize", $array)));
}

/**
 * Returns the direct items for a Project Item (Project, Category or File)
 *
 * @param $type
 * @param $id
 * @return mixed
 */
function getProjectItems($type, $id)
{
    /**
     * 1. We can't use Laravel collections because you can't merge 2 different collections.
     * 2. To do a UNION, have to select same number of columns - select AS NULL.
     * 3. If select isn't in EXACT SAME order, it will mess up the types of the values.
     */

    $categories = DB::table('project_categories')
                    ->where('parent_type', $type)
                    ->where('parent_id', $id)
                    ->selectRaw('
                                id,
                                created_at,
                                updated_at,
                                position,
                                name,
                                NULL AS description,
                                NULL AS weighting,
                                type,
                                NULL AS file_request_id,
                                project_id,
                                parent_type,
                                parent_id
                            ');

    $files = DB::table('project_files')
               ->where('parent_type', $type)
               ->where('parent_id', $id)
               ->selectRaw('
                            id,
                            created_at,
                            updated_at,
                            position,
                            name,
                            description,
                            weighting,
                            type,
                            file_request_id,
                            project_id,
                            parent_type,
                            parent_id
                    ');

    return $categories->union($files)->orderBy('position', 'asc')->get();
}

/**
 * Returns project Items as well as recursively returning all nested items.
 *
 * @param $type
 * @param $id
 * @return mixed
 */
function getProjectItemsWithNested($type, $id)
{
    $items = getProjectItems($type, $id);
    foreach ($items as $item) {
        $item->items = getProjectItemsWithNested($item->type, $item->id);
    }
    return $items;
}
