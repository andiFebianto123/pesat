<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class ChildMasterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'name' => 'required|min:5|max:255'
            'full_name'             =>  'required',
            'nickname'              =>  'required',
            'date_of_birth'         =>  'required',
            'registration_number'   =>  'required',
            'city_id'               =>  'required',
            'province_id'           =>  'required',
            'districts'             =>  'required',
            'father'                =>  'required',
            'mother'                =>  'required',
            'profession'            =>  'required',
            'economy'               =>  'required',
            'class'                 =>  'required',
            'school'                =>  'required',
            'school_year'           =>  'required'

        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
