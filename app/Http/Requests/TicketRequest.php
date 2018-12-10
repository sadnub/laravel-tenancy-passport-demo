<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        //Remove required from ticket updates
        if ($this->route()->getName() === 'tickets.update')
        {
            return [
                'title' => 'string',
                'contact' => 'string',
                'status' => 'string',
                'issue' => 'string'
            ];

        } else 
        {

            return [
                'title' => 'required|string',
                'contact' => 'required|string',
                'status' => 'required|string',
                'issue' => 'required|string'
            ];
        }
    }
}
