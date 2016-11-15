<?php

namespace App\Providers;

use App\Auth\CustomJWTGuard;
use App\Checklist;
use App\File;
use App\FileRequest;
use App\Note;
use App\Policies\ChecklistPolicy;
use App\Policies\FileRequestPolicy;
use App\Policies\FileUploadPolicy;
use App\Policies\NotePolicy;
use App\Policies\ProjectPolicy;
use App\Project;
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
        FileRequest::class => FileRequestPolicy::class,
        Project::class => ProjectPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Extend Custom JWT Auth Guard
        $this->app['auth']->extend('custom-jwt', function ($app, $name, array $config) {
            $guard = new CustomJWTGuard(
                $app['tymon.jwt'],
                $app['auth']->createUserProvider($config['provider']),
                $app['request']
            );

            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }
}
