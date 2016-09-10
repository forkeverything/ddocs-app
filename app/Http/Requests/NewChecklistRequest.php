<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class NewChecklistRequest extends FormRequest
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
            'recipients.*' => 'required|email',
            'name' => 'required'
        ];

        /**
         * TODO ::: (?) Update validation so that we'll only prevent duplicate checklist names if they are for the same recipient.
         */
    }

    public function messages()
    {
        return [
            'recipients.*.required' => 'Need at least one recipient.',
            'recipients.*.email' => 'Invalid recipient email.'
        ];
    }
}
