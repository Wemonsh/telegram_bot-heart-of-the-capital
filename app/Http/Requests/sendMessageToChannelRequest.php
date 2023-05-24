<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class sendMessageToChannelRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => 'string',
            'photo' => 'string',
            'chatId' => 'string'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
