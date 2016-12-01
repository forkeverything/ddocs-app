<?php


namespace App\Utilities\Traits;


use App\Checklist;
use App\FileRequest;
use App\Note;
use App\Recipient;
use Illuminate\Database\Eloquent\Model;

trait Hashable
{

    /**
     * Try to find a FileRequest by hash.
     *
     * @param $hash
     * @return Model | Checklist | FileRequest | Note | Recipient
     */
    public static function findByHash($hash) {
        $className = class_basename(get_called_class());
        $connection = snake_case($className);
        return static::findOrFail(unhashId($connection, $hash));
    }

    public function getHashAttribute()
    {
        $className = class_basename($this);
        $connection = snake_case($className);
        return hashId($connection, $this);
    }
}