<?php

return [
    'name' => 'SPOManagement',
    
    /*
    |--------------------------------------------------------------------------
    | Module Configuration
    |--------------------------------------------------------------------------
    */
    'module' => [
        'code' => 'spo-management',
        'slug' => 'spo-management',
        'name' => 'Manajemen SPO',
        'description' => 'Modul untuk mengelola Standar Prosedur Operasional (SPO) di rumah sakit',
        'icon' => 'file-text',
        'order' => 7,
    ],

    /*
    |--------------------------------------------------------------------------
    | Document Types
    |--------------------------------------------------------------------------
    */
    'document_types' => [
        'Kebijakan' => 'Kebijakan',
        'Pedoman' => 'Pedoman',
        'SPO' => 'SPO',
        'Perencanaan' => 'Perencanaan',
        'Program' => 'Program',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Validasi
    |--------------------------------------------------------------------------
    */
    'status_validasi' => [
        'Draft' => 'Draft',
        'Disetujui' => 'Disetujui',
        'Kadaluarsa' => 'Kadaluarsa',
        'Revisi' => 'Revisi',
    ],

    /*
    |--------------------------------------------------------------------------
    | Confidentiality Levels
    |--------------------------------------------------------------------------
    */
    'confidentiality_levels' => [
        'Internal' => 'Internal',
        'Publik' => 'Publik',
        'Rahasia' => 'Rahasia',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Values
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'document_type' => 'SPO',
        'document_version' => '1.0',
        'confidentiality_level' => 'Internal',
        'review_cycle_months' => 12,
    ],
];
