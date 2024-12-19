<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->sanitize();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = User::$rules;
        $rules['password'] = 'required|string|min:8|max:30|confirmed';
        $rules['photo'] = 'nullable|mimes:jpeg,jpg,png';

        return $rules;
    }

    public function messages(): array
    {
        return User::$messages;
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['name'] = htmlspecialchars($input['name']);
        $input['about'] = htmlspecialchars($input['about']);

        $this->replace($input);
    }
}
