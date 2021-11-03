<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsersRequest extends FormRequest
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
            'name'              =>  'required|max:255',
            'first_name'        =>  'max:255',
            'last_name'         =>  'max:255',
            'full_name'         =>  'required|max:255',
            'hometown'          =>  'max:255|',
            'date_of_birth'     =>  'nullable|date|date_format:Y-m-d',
            'address'           =>  'max:255',
            'no_hp'             =>  'required|max:255',
            'church_of_member'  =>  'max:255',
            'email'             =>  [ 'required','email','max:255',Rule::unique('users','email')->where(function($query){

                                        return $query;
                                        }
                                    )->ignore($this->id,'id')
                                    ],
            'password'          =>  'required|min:6|confirmed',
            'user_role_id'      =>  'required|regex:/^[0-9]+$/',
            'website_url'       =>  'max:255',
            'facebook_url'      =>  'max:255',
            'instagram_url'     =>  'max:255',
            'linkedin_url'      =>  'max:255',
            'my_space_url'      =>  'max:255',
            'pinterest_url'     =>  'max:255',
            'sound_cloud_url'   =>  'max:255',
            'tumblr_url'        =>  'max:255',
            'twitter_url'       =>  'max:255',
            'youtube_url'       =>  'max:255',
            'biograpical'       =>  'max:255',

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
