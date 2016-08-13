<?php

namespace App\Providers;

use App\Checklist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ChecklistProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('checklist_name', function ($attribute, $value, $parameters, $validator) {
            $recipient = array_get($validator->getData(), "recipient");
            return ! Checklist::where('name', $value)->where('user_id', Auth::user()->id)->where('recipient', $recipient)->get()->first();
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
