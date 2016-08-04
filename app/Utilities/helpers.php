<?php

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Returns the hash id for an Eloquent Model
 * @param Model $model
 * @return mixed
 */
function hashId(Model $model)
{
    return Hashids::encode($model->id);
}
