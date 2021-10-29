<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\ChildMaster;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChildMasterUpdateRequest extends FormRequest
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
        $id = $this->get('id') ?? request()->route('id');
        return [
           // 'registration_number'   => ['required', Rule::unique('child_master', 'registration_number')->ignore($id, 'child_id'),],
            'full_name'             =>  'required|max:255',
            'nickname'              =>  'required|max:255',
            'gender'                =>  'required|max:255',
            'hometown'              =>  'regex:/^[0-9]+$/',
            'date_of_birth'         =>  'required|date|date_format:Y-m-d',
            'religion_id'           =>  'regex:/^[0-9]+$/',
            'fc'                    =>  'max:255',
            'sponsor_name'          =>  'max:255',
            'city_id'               =>  'required',
            'districts'             =>  'required',            
            'province_id'           =>  'required',
            'father'                =>  'required|max:255',
            'mother'                =>  'required|max:255',
            'profession'            =>  'required|max:255',
            'economy'               =>  'required|max:255',
            'class'                 =>  'required|max:255',
            'school'                =>  'required|max:255',
            'school_year'           =>  'required|max:255',
            'sign_in_fc'            =>  'nullable|date_format:Y-m-d',
            'leave_fc'              =>  'nullable|date_format:Y-m-d',
            'reason_to_leave'       =>  'max:255', 
            'internal_discription'  =>  'max:255',
            'file_profile'          =>  'file|max:5000 (5000 kb)'


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
