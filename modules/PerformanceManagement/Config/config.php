<?php

return [
    'name' => 'PerformanceManagement',
    
    /*
    |--------------------------------------------------------------------------
    | Grade Configuration
    |--------------------------------------------------------------------------
    | Konfigurasi grading untuk penilaian kinerja
    */
    'grades' => [
        'A' => ['min' => 90, 'label' => 'Sangat Baik'],
        'B' => ['min' => 80, 'label' => 'Baik'],
        'C' => ['min' => 70, 'label' => 'Cukup'],
        'D' => ['min' => 60, 'label' => 'Kurang'],
        'E' => ['min' => 0, 'label' => 'Sangat Kurang'],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Measurement Types
    |--------------------------------------------------------------------------
    | Jenis-jenis pengukuran indikator kinerja
    */
    'measurement_types' => [
        'percentage' => 'Persentase (%)',
        'number' => 'Angka',
        'currency' => 'Mata Uang (Rp)',
        'time' => 'Waktu',
        'boolean' => 'Ya/Tidak',
        'custom' => 'Custom Formula',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    | Kategori indikator kinerja
    */
    'categories' => [
        'productivity' => 'Produktivitas',
        'quality' => 'Kualitas',
        'efficiency' => 'Efisiensi',
        'innovation' => 'Inovasi',
        'teamwork' => 'Kerjasama Tim',
        'leadership' => 'Kepemimpinan',
        'customer_service' => 'Pelayanan',
        'other' => 'Lainnya',
    ],
];
