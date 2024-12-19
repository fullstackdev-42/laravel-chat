<?php

namespace App\Http\Requests;

use App\Models\Group;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
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
        $rules = Group::$rules;
        $rules['name'] = 'nullable';
        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['description'] = htmlspecialchars($input['description']);

        $this->replace($input);
    }
}
