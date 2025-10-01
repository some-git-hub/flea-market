<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseRequest extends FormRequest
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
            'payment_method'   => [
                'required',
                'integer',
                Rule::in(array_keys(config('const.payment.methods'))),
            ],
        ];
    }

    public function withValidator($validator)
{
    $validator->after(function ($validator) {
        $postal = $this->input('postal_code');
        $address = $this->input('address');

        if (empty($postal) || empty($address)) {
            $validator->errors()->add('delivery', '配達先を選択してください');
        }
    });
}

    public function messages()
    {
        return [
            'payment_method.required' => ':attributeを選択してください',
            'payment_method.in'       => ':attributeを選択してください',
        ];
    }

    public function attributes()
    {
        return [
            'payment_method' => '支払い方法',
            'delivery'       => '配達先',
        ];
    }
}
