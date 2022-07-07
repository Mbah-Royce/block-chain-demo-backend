<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LandCertCreateRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            "reciever" => "required|string|exists:users,public_key",
            "area" => "required|numeric",
            "signature" => "required|string",
            "sender" => "required|string",
            "featureId" => "required|string",
            "geometryType" => "required|string",
            "geometryCoordinates" => "required|array"
        ];
    }
}
