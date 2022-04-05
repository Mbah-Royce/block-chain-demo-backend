<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class TransactionRequest extends FormRequest
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
            'type' => ['required',Rule::in(['whole-land', 'portion-portion','portion-title']),],
            'reciever' => 'required|string|exists:users,public_key',
            'sender' => 'required|string|exists:users,public_key',
            'signature' => 'required|string',
            'serial_no' => 'nullable|string|exists:land_certificates,serial_no',
            'partitions' => 'string',
            'area' => 'nullable|numeric',
            'partitionId' => 'nullable|exists:partitions,id'
        ];
    }
}
