<?php

namespace App\Providers;

use App\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        // Eloquent Events to keep FileRequest in sync whenever we modify it's File(s)

        File::created(function ($file) {
            if($file->fileRequest->files->count() > 1) $file->fileRequest->update(['version' => $file->fileRequest->version + 1]);
        });

        File::updated(function ($file) {
            $fileRequest = $file->fileRequest;
            if (! count($fileRequest->files()->where('rejected', 0)) > 0) $fileRequest->update(['status' => 'rejected']);
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
