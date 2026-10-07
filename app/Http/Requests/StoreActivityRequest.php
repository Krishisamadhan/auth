<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    /**
     * Kya ye request process karne ki permission hai?
     *
     * Abhi simple case mein true.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Form ke validation rules.
     */
    public function rules(): array
    {
        return [
            'activity_title' => 'required|string',
            'activity_date' => 'required|date',
            'location' => 'required|string',

            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }


    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'activity_title.required' => 'Please enter the activity title.',
            'activity_title.string' => 'Activity title must be text.',

            'activity_date.required' => 'Please select the activity date.',
            'activity_date.date' => 'Please enter a valid activity date.',

            'location.required' => 'Please enter the activity location.',
            'location.string' => 'Location must be text.',

            'image.required' => 'Please upload an activity image.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'Image must be JPG, JPEG, PNG, or WEBP.',
            'image.max' => 'Image size must not exceed 2MB.',
        ];
    }
}
