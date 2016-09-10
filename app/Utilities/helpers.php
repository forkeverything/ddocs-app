<?php

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Hash eloquent model id
 * @param $connection
 * @param Model $model
 * @return mixed
 */
function hashId($connection, Model $model)
{
    return Hashids::connection($connection)->encode($model->id);
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