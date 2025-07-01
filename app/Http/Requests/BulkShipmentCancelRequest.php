<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

    class BulkShipmentCancelRequest extends FormRequest
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
                'shipment_ids' => ['required', 'regex:/^\d+(,\d+)*$/'],
                'reason'       => 'required|string',
            ];
        }

        public function messages()
        {
            return [
                'shipment_ids.required' => 'Shipment IDs are required.',
                'shipment_ids.regex'    => 'Shipment IDs must be a comma-separated list of integers.',
                'reason.required'       => 'Reason is required.',
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
