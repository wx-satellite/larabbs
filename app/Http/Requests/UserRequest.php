<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */


    // authorize() 方法是表单验证自带的另一个功能 —— 权限验证，本课程中我们不会使用此功能，关于用户授权，我们将会在后面章节中使用更具扩展性的方案，
    // 此处我们 return true; ，意味所有权限都通过即可。
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
        // unique的用法：https://learnku.com/docs/laravel/5.8/validation/3899#rule-unique
        return [
            'name' => 'required|between:3,25|regex:/^[A-Za-z0-9\-\_]+$/',
            'email' => 'required|email|unique:users,email,'.Auth::id(),
            'introduction' => 'max:80',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => '用户名已被占用，请重新填写',
            'name.regex' => '用户名只支持英文、数字、横杠和下划线。',
            'name.between' => '用户名必须介于 3 - 25 个字符之间。',
            'name.required' => '用户名不能为空。',
        ];
    }
}
