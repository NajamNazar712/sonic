<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AddCrmRequest extends FormRequest
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

        $case_nature_id = (int) $this->input('case_nature_id');
        $complaint_id = (int) $this->input('complaint_id');
        $app_type = (int) $this->input('app_type');

        $rules = [
            'shipment_ids' => 'required', 'regex:/^([1-9]\d*)(,([1-9]\d*))*$/',
            'case_nature_id' => 'required|in:1,2,3,4',
            'complaint_id' =>  'required_unless:case_nature_id,4|integer',
            'description' => 'required|string',
        ];

        if($app_type == 2) {
            $rules['channel_id'] = 'required|integer';
        }

        if($app_type == 1) {
            switch ($case_nature_id) {
                case 1: //Complaint
                    $rules['complainant_phone'] = 'required|regex:/^03\d{2}-\d{7}$/';
                    $rules['case_nature_complainant'] = 'required|in:1,2';

                    break;

                case 2: //Service Request

                    if($complaint_id == 12) {
                        /**
                         * is_automated_cod_change
                         * is_zero_cod
                         * cod_parcel_value
                         * complainant_phone
                         * case_nature_complainant
                         **/
                        $rules['cod_new_amount'] =  'required|numeric|gt:0';
                        $rules['cod_remarks'] = 'required|string';

                    }

                    if($complaint_id == 13) {
                        $rules['alternate_phone'] = 'required|regex:/^03\d{2}-\d{7}$/';
                    }

                    break;

                case 4:

                    if($complaint_id != 26) {
                        $rules['claim_product_cost'] = 'required|numeric|min:0';
                        $rules['product_picture'] =  'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
                        $rules['invoice_picture'] =  'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
                    }
                    if($complaint_id == 21  || $complaint_id == 22) {
                        $rules['product_packaging_picture'] =  'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
                        $rules['actual_product_picture'] =  'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
                    }

                    if($complaint_id == 21) {
                        $rules['damage_product_picture'] =  'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
                        $rules['damage_claim_product_cost'] = 'required|numeric|min:0';
                    }
                    if ($complaint_id == 22) {
                        $rules['missing_product_picture'] =  'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
                        $rules['claim_content_product_cost'] = 'required|numeric|min:0';
                    }
//
                    if( $complaint_id == 23) {
                        $rules['receiving_sheet_id'] = 'required|integer|gt:0';
                    }

                    break;

            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            // Generic
            'shipment_ids.required' => 'At least one shipment must be selected.',
            'shipment_ids.array' => 'Shipment IDs must be an array.',
            'shipment_ids.min' => 'Select at least one shipment.',

            'case_nature_id.required' => 'Case nature is required.',
            'case_nature_id.in' => 'Invalid case nature selected.',
            'complaint_id.required' => 'Complaint ID is required.',
            'description.required' => 'Description is required.',

            //for retail app
            'channel_id.required' => 'Channel ID is required',
            'channel_id.integer' => 'Channel ID must be a valid number.',

            // Phone numbers
            'complainant_phone.required' => 'Complainant phone is required.',
            'complainant_phone.regex' => 'Complainant phone must be in format 03xx-xxxxxxx.',
            'alternate_phone.required' => 'Alternate phone is required.',
            'alternate_phone.regex' => 'Alternate phone must be in format 03xx-xxxxxxx.',

            // COD related
            'cod_new_amount.required' => 'New COD amount is required.',
            'cod_new_amount.numeric' => 'COD amount must be numeric.',
            'cod_new_amount.gt' => 'COD amount must be greater than 0.',
            'cod_remarks.required' => 'COD remarks are required.',

            // Claim (case_nature_id = 4)
            'claim_product_cost.required'      => 'Claim product cost is required.',
            'claim_product_cost.numeric'       => 'Claim product cost must be a number.',
            'claim_product_cost.min'           => 'Claim product cost must be at least 0.',

            // Product images
            'product_picture.required'         => 'Product picture is required.',
            'product_picture.image'            => 'Product picture must be an image.',
            'product_picture.mimes'            => 'Product picture must be a file of type: jpeg, png, jpg, gif, webp.',
            'product_picture.max'              => 'Product picture must not be greater than 2MB.',

            // Invoice images
            'invoice_picture.required'         => 'Invoice picture is required.',
            'invoice_picture.image'            => 'Invoice picture must be an image.',
            'invoice_picture.mimes'            => 'Invoice picture must be a file of type: jpeg, png, jpg, gif, webp.',
            'invoice_picture.max'              => 'Invoice picture must not be greater than 2MB.',

            // Product Packaging images
            'product_packaging_picture.required' => 'Packaging picture is required.',
            'product_packaging_picture.image'    => 'Packaging picture must be an image.',
            'product_packaging_picture.mimes'    => 'Packaging picture must be a file of type: jpeg, png, jpg, gif, webp.',
            'product_packaging_picture.max'      => 'Packaging picture must not be greater than 2MB.',

            // Actual Product images
            'actual_product_picture.required'  => 'Actual product picture is required.',
            'actual_product_picture.image'     => 'Actual product picture must be an image.',
            'actual_product_picture.mimes'     => 'Actual product picture must be a file of type: jpeg, png, jpg, gif, webp.',
            'actual_product_picture.max'       => 'Actual product picture must not be greater than 2MB.',

            // Damage claim (complaint_id = 21)
            'damage_product_picture.required'  => 'Damage picture is required.',
            'damage_product_picture.image'     => 'Damage picture must be an image.',
            'damage_product_picture.mimes'     => 'Damage picture must be a file of type: jpeg, png, jpg, gif, webp.',
            'damage_product_picture.max'       => 'Damage picture must not be greater than 2MB.',

            'damage_claim_product_cost.required' => 'Damage claim product cost is required.',
            'damage_claim_product_cost.numeric'  => 'Damage claim product cost must be a number.',
            'damage_claim_product_cost.min'      => 'Damage claim product cost must be at least 0.',

            // Missing claim (complaint_id = 22)
            'missing_product_picture.required'   => 'Missing product picture is required.',
            'missing_product_picture.image'      => 'Missing product picture must be an image.',
            'missing_product_picture.mimes'      => 'Missing product picture must be a file of type: jpeg, png, jpg, gif, webp.',
            'missing_product_picture.max'        => 'Missing product picture must not be greater than 2MB.',

            'claim_content_product_cost.required' => 'Claim content product cost is required.',
            'claim_content_product_cost.numeric'  => 'Claim content product cost must be a number.',
            'claim_content_product_cost.min'      => 'Claim content product cost must be at least 0.',

            // Receiving sheet (complaint_id = 23)
            'receiving_sheet_id.required'       => 'Receiving sheet ID is required.',
            'receiving_sheet_id.integer'        => 'Receiving sheet ID must be a valid number.',
            'receiving_sheet_id.gt'             => 'Receiving sheet ID must be greater than 0.',

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
