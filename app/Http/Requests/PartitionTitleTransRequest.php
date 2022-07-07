<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartitionTitleTransRequest extends FormRequest
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
            "signature" => "required|string",
        
            'serial_no' => 'required',

            'reciever_pub' => 'required',
            'reciever_feature_area' => 'required',
            'reciever_feature_id' => 'required',
            'reciever_feature_coordinates' => 'required',
            'reciever_feature_type' => 'required',

            'sender_pub' => 'required',
            'sender_feature_area' => 'required',
            'sender_feature_type' => 'required',
            'sender_feature_coordinate_length' => 'required',
            'sender_feature_coordinates' => 'required'
        ];
    }
}
