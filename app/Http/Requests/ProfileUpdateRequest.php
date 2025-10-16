<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'no_whatsapp' => [
                'required',
                'string',
                'regex:/^08[0-9]{9,11}$/',
                Rule::unique('users', 'no_whatsapp')->ignore($this->user()->id),
            ],
            'alamat' => ['required', 'string', 'min:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'no_whatsapp.unique' => 'Nomor WhatsApp ini sudah digunakan oleh pengguna lain.',
            'no_whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'no_whatsapp.regex' => 'Format nomor WhatsApp harus diawali dengan 08 dan terdiri dari 11 hingga 13 digit angka.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.min' => 'Alamat minimal 5 karakter.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $noWa = $this->input('no_whatsapp');
            $alamat = $this->input('alamat');

            // Nomor WA sudah digunakan oleh user lain
            if ($validator->errors()->has('no_whatsapp')) {
                if (
                    $validator->errors()->first('no_whatsapp') === 'Nomor WhatsApp ini sudah digunakan oleh pengguna lain.'
                ) {
                    Redirect::to(route('auth.wasudahdipake'))->send();
                    exit;
                }
            }

            // Panjang nomor WA tidak sesuai (harus 11–13 digit)
            $cleaned = preg_replace('/[^0-9]/', '', $noWa);
            $length = strlen($cleaned);
            if ($length < 11 || $length > 13) {
                Redirect::to(route('auth.nomorkurang'))->send();
                exit;
            }

            // Alamat tidak diisi atau terlalu pendek
            if (empty($alamat) || strlen($alamat) < 5) {
                Redirect::to(route('auth.alamatbelumdiisi'))->send();
                exit;
            }
        });
    }
}
