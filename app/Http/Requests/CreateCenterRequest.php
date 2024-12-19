<?php

namespace App\Http\Requests;

use App\Models\Center;
use Illuminate\Foundation\Http\FormRequest;

class CreateCenterRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        $this->sanitize();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = Center::$rules;
        $rules['name'] = 'required|string|max:100';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['name'] = htmlspecialchars($input['name']);

        $this->replace($input);
    }
}
