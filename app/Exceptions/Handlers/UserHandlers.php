<?php

namespace App\Exceptions\Handlers;

use App\Events\UserHasRunOutOfCredits;
use Illuminate\Support\Facades\Event;

trait UserHandlers
{
    protected function handleNotEnoughCredits($request, \Exception $exception)
    {
        Event::fire(new UserHasRunOutOfCredits($exception->user));

        if($request->ajax()) {
            return response([
                'credits' => [
                    'Not enough credits to create list!'
                ]
            ], 402);
        }
        
        return redirect()->back()->withErrors(['credits' => 'Not enough credits to create list!']);
    }
}