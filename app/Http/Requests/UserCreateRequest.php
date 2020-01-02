<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserCreateRequest extends FormRequest
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
        return [
            'email' => [
				'required',
				'email',
				// 唯一性检查，排除软删除字段
				Rule::unique('users')->where(function ($query) {
					$query->where('deleted_at', null);
				}),
			],
            'phone'   => [
            	'required',
				'numeric',
				'regex:/^1[3456789][0-9]{9}$/',
				Rule::unique('users')->where(function ($query) {
					$query->where('deleted_at', null);
				}),
				],
//            'username'  => 'required|min:4|max:14|unique:users',
			'username' => [
				'required',
				'min:4',
				'max:14',
				Rule::unique('users')->where(function ($query) {
					$query->where('deleted_at', null);
				}),
			],
            'password'  => 'required|confirmed|min:6|max:14'
        ];
    }
}
