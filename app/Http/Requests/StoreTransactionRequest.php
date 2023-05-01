<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'value' => 'required|numeric|min:0.01|decimal:0,2',
            "payer.id" => [
                'required',
                'numeric',
                'exists:users,id'
            ],
            'payee.id' => [
                'required',
                'numeric',
                'different:payer.id',
                'exists:users,id'
            ]
        ];
    }

    public function after()
    {
        $payer = User::withWhereHas('wallet')->findOrFail($this->json('payer.id'));
        $value = $this->float('value');
        return [
            function (Validator $validator) use ($payer,$value) {
                $errors = [];
                if (!$payer->canTransfer()) {
                    $errors['payer.id'] = "User is not allowed to transfer";
                }
                if (!$payer->hasBalance($value)) {
                    $errors['value'] = "Insuficient Balance";
                }
                if (!empty($errors)) {
                    foreach ($errors as $field => $message) {
                        $validator->errors()->add($field, $message);
                    }
                }
            }
        ];
    }
}
