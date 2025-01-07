<?php

return [
    'required' => ':attribute harus diisi.',
    'email' => 'Format :attribute tidak valid.',
    'alpha_num' => 'Format :attribute tidak valid, gunakan hanya huruf dan angka.',
    'regex' => 'Format :attribute tidak valid.',
    'unique' => ':attribute sudah digunakan.',
    'max' => [
        'string' => ':attribute terlalu panjang, maksimal :max karakter.',
    ],
    'min' => [
        'string' => ':attribute terlalu pendek, minimal :min karakter.',
    ],
    'custom' => [
        'password' => [
            'confirmed' => 'Kata sandi yang anda konfirmasi salah.',
        ],
    ],
    'attributes' => [
        'name' => 'Nama',
        'email' => 'Email',
        'phone' => 'No Telepon',
        'address' => 'Alamat',
    ],

];
