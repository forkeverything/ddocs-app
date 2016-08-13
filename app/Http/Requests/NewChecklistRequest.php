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
            'name' => 'required|checklist_name',
            'requested_files.*.name' => 'required'
        ];

        /**
         * TODO ::: (?) Update validation so that we'll only prevent duplicate checklist names if they are for the same recipient.
         */
    }

    public function messages()
    {
        return [
            'recipient.required' => 'Recipient to send list to cannot be empty.',
            'recipient.email' => 'Invalid recipient email address.',
            'name.checklist_name' => 'Can\'t create checklist with same name for the same recipient.'
        ];
    }
}
