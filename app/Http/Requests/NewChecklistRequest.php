<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class NewChecklistRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Anyone can make a new checklist
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'recipient' => 'required|email',
            'name' => 'required|unique:checklists,name,NULL,id,user_id,' . Auth::user()->id,
            'requested_files.*.name' => 'required'
        ];

        /**
         * TODO ::: (?) Update validation so that we'll only prevent duplicate checklist names if they are for the same recipient.
         */
    }
}
