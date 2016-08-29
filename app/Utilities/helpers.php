<?php

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Returns the hash id for an Eloquent Model
 *
 * @param Model $model
 * @return mixed
 */
function hashId(Model $model)
{
    return Hashids::encode($model->id);
}

/**
 * Takes a hashed Id and decodes it to return an Eloquent Model Id
 *
 * @param $hash
 * @return mixed
 */
function unhashId($hash)
{
    return array_first(Hashids::decode($hash));
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
