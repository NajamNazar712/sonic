<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidateShipmentIdRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'shipment_id' => ['required', 'integer'],
        ];
    }

    public function messages()
    {
        return [
            'shipment_id.required' => 'The shipment ID is required.',
            'shipment_id.integer' => 'The shipment ID must be an integer.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 1,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
