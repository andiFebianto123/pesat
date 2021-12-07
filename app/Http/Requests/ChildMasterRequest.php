<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            //'registration_number'   =>  'required|unique:Child_Master',
            'registration_number'   => ['required', Rule::unique('child_master','registration_number')->where(function($query){

                return $query->where('is_active',1);
            }
            )->ignore($this->child_id,'child_id')],
            'full_name'             =>  'required|max:255',
            'nickname'              =>  'required|max:255',
            'gender'                =>  'required|max:255',
            'hometown'              =>  'regex:/^[0-9]+$/',
            'date_of_birth'         =>  'required|date|date_format:Y-m-d',
            'religion_id'           =>  'regex:/^[0-9]+$/',
            'fc'                    =>  'max:255',
            'price'                 =>  'required|integer|min:1|regex:/^-?[0-9]+(?:\.[0-9]{1,2})?$/',
            'sponsor_name'          =>  'max:255',
            'city_id'               =>  'required|regex:/^[0-9]+$/',
            'districts'             =>  'required|max:255',
            'province_id'           =>  'required|regex:/^[0-9]+$/',
            'father'                =>  'required|max:255',
            'mother'                =>  'required|max:255',
            'profession'            =>  'required|max:255',
            'economy'               =>  'required|max:255',
            'class'                 =>  'required|max:255',
            'school'                =>  'required|max:255',
            'school_year'           =>  'required|max:255',
            'sign_in_fc'            =>  'nullable|date|date_format:Y-m-d',
            'leave_fc'              =>  'nullable|date|date_format:Y-m-d',
            'reason_to_leave'       =>  'max:255', 
            'internal_discription'  =>  'max:255',
            'file_profile'          =>  'file|max:5000'
            
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
