# Performance Management Module

Modul untuk mengelola indikator kinerja individu (KPI), penilaian, dan template.

## Fitur

- **Indikator Kinerja (KPI)**: Kelola indikator kinerja dengan berbagai jenis pengukuran
- **Template KPI**: Atur template KPI berdasarkan role/jabatan
- **Penilaian Kinerja**: Catat dan kelola penilaian kinerja karyawan
- **Multi-tenant**: Mendukung isolasi data per tenant
- **Audit Trail**: Pencatatan aktivitas lengkap

## Models

- `PerformanceIndicator`: Indikator kinerja
- `PerformanceTemplate`: Template KPI per role
- `PerformanceScore`: Nilai/skor kinerja individual

## Database Tables

- `performance_indicators`: Menyimpan definisi indikator kinerja
- `performance_templates`: Template KPI per role
- `performance_scores`: Nilai kinerja individual

## Routes

Semua routes menggunakan prefix `/performance-management`

## Permissions

Module menggunakan permission middleware `check.permission:performance-management,{action}`
