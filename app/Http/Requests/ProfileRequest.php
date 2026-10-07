<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
                 'state' => 'required', 
            'district' => 'required',
            'tehsil' => 'required',
            'village' => 'required',
            'panchayat' => 'required',
            'pincode' => 'required|digits:6',

            'mobile_no' => 'required|integer',
            'address' => 'required',
         'operation_level' => 'nullable|in:village,block,district,state',


            'image' => 'required|image|mimes:jpg, jpeg,png,webp|max:2048'

        ];
    }

 public function messages(): array
{
    return [
        'state.required' => 'Please enter the State.',
        'district.required' => 'Please enter the District.',
        'tehsil.required' => 'Please enter the Tehsil.',
        'village.required' => 'Please enter the Village.',
        'panchayat.required' => 'Please enter the Panchayat.',
        'pincode.required' => 'Please enter the Pincode.',
        'pincode.digits' => 'Pincode must be exactly 6 digits.',

        'mobile_no.required' => 'Please enter the Mobile Number.',
        'mobile_no.digits' => 'Mobile Number must be exactly 10 digits.',

        'address.required' => 'Please enter the Address.',

        'operation_level.in' => 'Please select a valid operation level.',

        'image.required' => 'Please upload an image.',
        'image.image' => 'The uploaded file must be an image.',
        'image.mimes' => 'Image must be a JPG, JPEG, PNG, or WEBP file.',
        'image.max' => 'Image size must not exceed 2 MB.',
    ];
}

}
