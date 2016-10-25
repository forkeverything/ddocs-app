<?php


namespace App\Utilities\Traits;


use Storage;

trait HasUploads
{
    /**
     * Deletes physical files, upload models and finally the parent model.
     *
     * @return bool|null
     * @throws \Exception
     */
    public function fullDelete()
    {
        $uploadPaths = $this->uploads->pluck('path')->toArray();
        foreach ($this->uploads as $upload) {
            if(Storage::delete($upload->path)) $upload->delete();
        }
        return $this->delete();
    }
}