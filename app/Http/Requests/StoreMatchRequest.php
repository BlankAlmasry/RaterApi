<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMatchRequest extends FormRequest
{
    protected $users = [];
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
     * @return array
     */
    public function rules()
    {
        return [
            'teams' => ['required', 'array', 'size:2',
                // Assert each team have the same number of players
                function ($attribute, $value, $fail) {
                    if (count($value[0]['users']) !== count($value[1]['users'])) {
                        $fail('The ' . $attribute . ' are unbalanced.');
                    }
                },
                // Assert no duplicates
                function ($attribute, $value, $fail){
                    $this->users = array_merge($value[0]['users'], $value[1]['users']);
                    if (count($this->users ) !== count(array_unique($this->users))){
                        $fail('Duplicated Users Entry.');
                    }
                },
            ],
            'teams.*.users' => ['required', 'array'],
            'teams.*.users.*'=> 'string|required',
            'teams.*.result' => 'required|numeric',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 400));
    }

}
