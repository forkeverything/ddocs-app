<?php

namespace App\Providers;

use App\Checklist;
use App\Recipient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ChecklistServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        // TODO ::: To turn prevent duplicate lists for single recipient

//        Validator::extend('checklist_name', function ($attribute, $value, $parameters, $validator) {
//            $recipients = array_get($validator->getData(), "recipients");
//            foreach($recipients as $recipient) {
//
//                $recipientWithSameChecklistName = !! Recipient::where('email', $recipient)
//                    ->whereExists(function ($query) use ($value) {
//                        $query->select(DB::raw(1))
//                              ->from('checklists')
//                              ->where('user_id', '=', Auth::id())
//                              ->where('name', $value)
//                              ->whereRaw('recipients.checklist_id = checklists.id');
//                    })
//                    ->select(DB::raw(1))
//                    ->first();
//
//                if($recipientWithSameChecklistName) return false;
//            }
//        });
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
