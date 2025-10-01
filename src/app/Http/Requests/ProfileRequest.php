<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'profile_image' => 'nullable|image|mimes:jpeg,png|max:2048',
            'name'          => 'required|string|max:20',
            'postal_code'   => 'required|size:8|regex:/^\d{3}-\d{4}$/',
            'address'       => 'required|string|max:255',
            'building'      => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'postal_code.size'     => ':attributeはハイフンありの:size文字で入力してください',
            'postal_code.regex'    => ':attributeは「123-4567」の形式で入力してください',
        ];
    }

    public function attributes()
    {
        return [
            'profile_image' => 'プロフィール画像',
            'name'          => 'お名前',
            'postal_code'   => '郵便番号',
            'address'       => '住所',
            'building'      => '建物名',
        ];
    }
}
