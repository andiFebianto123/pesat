<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectMasterRequest extends FormRequest
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
            'title'          => ['required','max:255',Rule::unique('project_master','title')->where(function($query){

                                    return $query->where('deleted_at',null);
                                })->ignore($this->project_id,'project_id')
                                ],
            'discription'    => 'required',
            'featured_image' => 'required',
            'start_date'     => 'required|date|date_format:Y-m-d',
            'end_date'       => 'nullable|date|date_format:Y-m-d',
            'amount'         => 'required|integer|min:1|regex:/^-?[0-9]+(?:\.[0-9]{1})?$/'
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
