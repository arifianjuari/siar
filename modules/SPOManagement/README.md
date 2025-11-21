# SPO Management Module

## Deskripsi

Modul untuk mengelola Standar Prosedur Operasional (SPO) di rumah sakit.

## Fitur

- CRUD SPO (Create, Read, Update, Delete)
- Dashboard SPO dengan statistik
- Filter berdasarkan Work Unit, Status, dan Tipe Dokumen
- Generate PDF dokumen SPO
- Generate QR Code untuk SPO
- Multi-tagging untuk dokumen
- Linked units (SPO dapat dikaitkan dengan beberapa unit kerja)
- Review cycle management
- Status validasi (Draft, Disetujui, Kadaluarsa, Revisi)
- Tingkat kerahasiaan (Internal, Publik, Rahasia)

## Struktur Modul

```
SPOManagement/
├── Config/
│   └── config.php              # Konfigurasi modul
├── Database/
│   ├── Migrations/             # Database migrations
│   └── Seeders/                # Database seeders
├── Http/
│   ├── Controllers/
│   │   └── SPOController.php   # Controller utama
│   ├── Middleware/             # Custom middleware
│   ├── Requests/               # Form requests
│   └── routes.php              # Routes definition
├── Models/
│   └── SPO.php                 # Model SPO
├── Policies/
│   └── SPOPolicy.php           # Authorization policy
├── Providers/
│   ├── SPOManagementServiceProvider.php
│   └── RouteServiceProvider.php
├── Resources/
│   └── Views/                  # Blade templates
├── Services/                   # Business logic services
└── Tests/                      # Unit & feature tests
```

## Routes

- `GET /spo` - Daftar SPO
- `GET /spo/dashboard` - Dashboard SPO
- `GET /spo/create` - Form create SPO
- `POST /spo` - Store SPO
- `GET /spo/{spo}` - Detail SPO
- `GET /spo/{spo}/edit` - Form edit SPO
- `PUT /spo/{spo}` - Update SPO
- `DELETE /spo/{spo}` - Delete SPO
- `GET /spo/{spo}/generate-pdf` - Generate PDF
- `GET /spo/{spo}/qr-code` - Generate QR Code

## Permissions

Module ini menggunakan permission `spo-management`:

- `can_view` - Melihat daftar dan detail SPO
- `can_create` - Membuat SPO baru
- `can_edit` - Mengedit SPO
- `can_delete` - Menghapus SPO

## Dependencies

- `barryvdh/laravel-dompdf` - PDF generation
- `simplesoftwareio/simple-qrcode` - QR code generation

## Migrasi dari WorkUnit Module

Module ini di-refactor dari WorkUnit module untuk memberikan pemisahan concern yang lebih baik.
SPO adalah entitas independen yang berelasi dengan Work Unit, bukan sub-feature dari Work Unit.

## Backward Compatibility

Model `App\Models\SPO` tetap tersedia sebagai adapter untuk backward compatibility.
