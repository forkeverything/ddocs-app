<?php


namespace App\Utilities\Traits;

use App\Jobs\DeleteStorageFiles;

trait HasUploads
{

    /**
     * The last uploaded file.
     *
     * @return mixed
     */
    public function getLatestUploadAttribute()
    {
        return $this->uploads()->orderBy('created_at', 'desc')->get()->first();
    }

    /**
     * Delete physical files as well as the Upload models.
     */
    public function deleteUploads()
    {
        $uploads = $this->uploads;
        $uploadPaths = $uploads->pluck('path')->toArray();
        dispatch(new DeleteStorageFiles($uploadPaths));
        foreach ($uploads as $upload) {
            $upload->delete();
        }
    }
}