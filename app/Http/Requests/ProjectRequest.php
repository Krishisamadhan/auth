<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
        'project_title'=>'required|string|max:255',
        'project_description'=>'required|string',
        'project_location'=>'required|string|max:255',
        'started_date'=>'required|date',
        'end_date'=>'required|date|after_or_equal:started_date',
        'remarks'=>'nullable|string',
        'status'=>'sometimes|in:pending,ongoing,completed',
    ];
    }


    public function messages():array
    {
        return [
                  'project_title.required' =>'please enter the title',

                  'project_description.required' => 'please enter the description',

                 'project_location.required' => 'please enter the location',

                 'started_date.required' => 'please enter the starting date',

                 'end_date.required' => 'please enter the end date',
        ];
    }
}
