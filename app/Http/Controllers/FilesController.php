<?php

namespace App\Http\Controllers;

use App\Repositories\FilesRepository;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class FilesController extends Controller
{

    /**
     * Retrieve all File(s) for the authenticated User.
     *
     * @return $this
     */
    public function getForUser()
    {
        $repository = FilesRepository::forUser(Auth::user())
                                     ->searchFor(request('search'))
                                     ->sortOn(request('sort'), request('order'))
                                     ->take(request('limit'));

        if (request('name_only')) {
            return $repository->select('name')->getWithoutQueryProperties()->pluck('name');
        }

        return $repository->get();
    }
}
