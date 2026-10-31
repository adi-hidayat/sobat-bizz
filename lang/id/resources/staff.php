<?php

return [

    'model_label' => 'Staf',

    'plural_model_label' => 'Staf',

    'sections' => [
        'employment' => 'Data Kepegawaian',
        'account' => 'Data Akun',
    ],

    'fields' => [
        'employee_number' => 'Nomor Induk Pegawai (NIP)',
        'department' => 'Departemen',
        'joined_at' => 'Tanggal Masuk',
        'photo' => 'Foto profil',
    ],

    'form' => [
        'password_helper' => 'Kosongkan jika tidak ingin mengubah password.',
    ],

    'status' => [
        'active' => 'Aktif',
        'inactive' => 'Nonaktif',
    ],

];
