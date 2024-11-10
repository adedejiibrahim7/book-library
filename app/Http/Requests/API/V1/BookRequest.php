<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $is_required = $this->method() == 'POST' ? 'required' : 'sometimes';
        return [
            'title' => [$is_required, 'string', 'max:255'],
            'author' => [$is_required, 'string', 'max:255'],
            'isbn' => [$is_required, 'string', 'max:255'],
            'published_date' => ['required']
        ];
    }
}
