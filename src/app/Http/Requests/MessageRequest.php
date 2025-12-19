<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
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
            'content' => 'required|string|max:400',
            'image'   => 'nullable|image|mimes:jpeg,png|max:10240',
        ];
    }

    public function attributes()
    {
        return [
            'content' => '本文',
            'image'   => '画像',
        ];
    }

    public function messages()
    {
        return [
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください'
        ];
    }
}
