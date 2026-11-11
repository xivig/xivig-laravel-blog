<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactSubmitRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'name'             => 'required|string|max:120',
            'email'            => 'required|email|max:150',
            'subject'          => 'nullable|string|max:200',
            'message'          => 'required|string|min:10|max:3000',
            'contact_honeypot' => 'nullable|string|max:0', // Must be empty
        ];
    }
}
