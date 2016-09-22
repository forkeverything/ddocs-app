<?php

namespace App\Providers;

use App\Checklist;
use App\File;
use App\FileRequest;
use App\Note;
use App\Policies\ChecklistPolicy;
use App\Policies\FileRequestPolicy;
use App\Policies\FileUploadPolicy;
use App\Policies\NotePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Checklist::class => ChecklistPolicy::class,
        File::class => FileUploadPolicy::class,
        FileRequest::class => FileRequestPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
