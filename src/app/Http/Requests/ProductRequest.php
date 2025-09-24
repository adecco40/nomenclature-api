<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|uuid|exists:categories,id',
            'supplier_id' => 'nullable|uuid|exists:suppliers,id',
            'price' => 'required|numeric|min:0',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->messages();
        $response = response()->json([
            'message' => 'Ошибка валидации',
            'data' => $errors,
            'timestamp' => now()->toIso8601String(),
            'success' => false
        ], 422);

        throw new HttpResponseException($response);
    }
}
