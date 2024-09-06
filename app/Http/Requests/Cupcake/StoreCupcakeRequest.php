<?php

namespace App\Http\Requests\Cupcake;

use Illuminate\Foundation\Http\FormRequest;


class StoreCupcakeRequest extends FormRequest
{
    // /**
    //  * Determine if the user is authorized to make this request.
    //  */
    // public function authorize(): bool
    // {
    //     return true;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => "required|string",
            "price_in_cents" => 'required|gt:0',
            "photo_url" => 'required|string',
            "description" => 'required|string',
            "quantity" => 'required|integer|gte:0',
            "is_available" => 'required|boolean',
            "is_advertised" => 'required|boolean' 
        ];
    }
}
