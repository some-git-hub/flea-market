<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExhibitionRequest extends FormRequest
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
            'item_image'  => 'required|image|mimes:jpeg,png|max:2048',
            'category'    => 'required|array|min:1',
            'category.*'  => 'exists:categories,id',
            'condition'   => [
                'required',
                'integer',
                Rule::in(array_keys(config('const.item.conditions'))),
            ],
            'name'        => 'required|string|max:255',
            'brand'       => 'nullable|string|max:255',
            'description' => 'required|string|max:255',
            'price'       => 'required|integer|min:50|max:9999999999',
        ];
    }

    public function messages()
    {
        return [
            'item_image.required'  => ':attributeを指定してください',
            'category.required'    => ':attributeを選択してください',
            'category.min'         => ':attributeを:minつ以上選択してください',
            'condition.required'   => ':attributeを選択してください',
            'price.min'            => ':attributeは:min以上の整数で入力してください',
            'price.max'            => ':attributeは:max以下の整数で入力してください',
        ];
    }

    public function attributes()
    {
        return [
            'item_image'  => '商品画像',
            'category'    => 'カテゴリー',
            'condition'   => '商品の状態',
            'name'        => '商品名',
            'brand'       => 'ブランド名',
            'description' => '商品の説明',
            'price'       => '販売価格',
        ];
    }
}
