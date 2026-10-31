<?php

return [

    'model_label' => 'Staff',

    'plural_model_label' => 'Staff',

    'sections' => [
        'employment' => 'Employment Data',
        'account' => 'Account Data',
    ],

    'fields' => [
        'employee_number' => 'Employee Number (NIP)',
        'department' => 'Department',
        'joined_at' => 'Join Date',
        'photo' => 'Profile photo',
    ],

    'form' => [
        'password_helper' => 'Leave blank if you do not want to change the password.',
    ],

    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],

];
