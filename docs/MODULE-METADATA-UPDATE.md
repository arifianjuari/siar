# Update Module Metadata - Bahasa Indonesia & Icons

**Tanggal**: 21 November 2025  
**Tujuan**: Standardisasi metadata modul dengan nama Bahasa Indonesia dan ikon yang sesuai

## Perubahan yang Dilakukan

Semua file `module.json` di folder `modules/` telah diperbarui dengan:

- ✅ **Nama** dalam Bahasa Indonesia
- ✅ **Icon** yang sesuai dengan fungsi modul
- ✅ **Description** dalam Bahasa Indonesia
- ✅ **Version** standardisasi ke 1.0.0
- ✅ **Keywords** ditambahkan kata kunci Bahasa Indonesia

## Daftar Modul yang Diperbarui

| No  | Nama Modul               | Slug                   | Icon                      | Code (Auto)            |
| --- | ------------------------ | ---------------------- | ------------------------- | ---------------------- |
| 1   | **Manajemen Pengguna**   | user-management        | `fa-users-cog`            | USER_MANAGEMENT        |
| 2   | **Manajemen Aktivitas**  | activity-management    | `fa-tasks`                | ACTIVITY_MANAGEMENT    |
| 3   | **Manajemen Risiko**     | risk-management        | `fa-exclamation-triangle` | RISK_MANAGEMENT        |
| 4   | **Manajemen Dokumen**    | document-management    | `fa-folder-open`          | DOCUMENT_MANAGEMENT    |
| 5   | **Manajemen Produk**     | product-management     | `fa-boxes`                | PRODUCT_MANAGEMENT     |
| 6   | **Unit Kerja**           | work-unit              | `fa-sitemap`              | WORK_UNIT              |
| 7   | **Surat Menyurat**       | correspondence         | `fa-envelope-open-text`   | CORRESPONDENCE         |
| 8   | **Manajemen SPO**        | spo-management         | `fa-file-medical-alt`     | SPO_MANAGEMENT         |
| 9   | **Kendali Mutu & Biaya** | kendali-mutu-biaya     | `fa-chart-line`           | KENDALI_MUTU_BIAYA     |
| 10  | **Manajemen Kinerja**    | performance-management | `fa-chart-bar`            | PERFORMANCE_MANAGEMENT |

## Penjelasan Field

### 1. **name** (Nama Modul)

- Ditampilkan di UI dalam Bahasa Indonesia
- Contoh: "Manajemen Pengguna", "Surat Menyurat"

### 2. **alias** (Slug)

- Digunakan untuk routing dan identifier
- Format: kebab-case (lowercase dengan dash)
- Contoh: `user-management`, `correspondence`
- **TIDAK DIUBAH** untuk menjaga konsistensi routing

### 3. **icon** (Ikon FontAwesome)

- Menggunakan FontAwesome 5 class
- Format: `fa-{icon-name}`
- Dipilih sesuai konteks modul

### 4. **code** (Kode Unik)

- Auto-generated dari slug
- Format: UPPERCASE dengan underscore
- Contoh: `USER_MANAGEMENT`, `CORRESPONDENCE`
- Dibuat otomatis saat sync dari filesystem

### 5. **description**

- Deskripsi modul dalam Bahasa Indonesia
- Menjelaskan fungsi utama modul

## Icon Reference

Berikut referensi ikon yang dipilih:

| Icon Class                | Visual | Digunakan Untuk                |
| ------------------------- | ------ | ------------------------------ |
| `fa-users-cog`            | 👥⚙️   | Manajemen Pengguna             |
| `fa-tasks`                | ☑️     | Manajemen Aktivitas/Tugas      |
| `fa-exclamation-triangle` | ⚠️     | Manajemen Risiko               |
| `fa-folder-open`          | 📂     | Manajemen Dokumen              |
| `fa-boxes`                | 📦     | Manajemen Produk/Inventori     |
| `fa-sitemap`              | 🏢     | Unit Kerja/Struktur Organisasi |
| `fa-envelope-open-text`   | ✉️     | Surat Menyurat                 |
| `fa-file-medical-alt`     | 📋     | SPO (Standar Prosedur)         |
| `fa-chart-line`           | 📈     | Kendali Mutu & Biaya           |
| `fa-chart-bar`            | 📊     | Manajemen Kinerja/KPI          |

## Cara Sync ke Database

Setelah update file `module.json`, jalankan sync untuk update database:

### Via Web Interface:

1. Buka: `http://siar.test/superadmin/modules`
2. Klik tombol **"Sync dari Filesystem"**
3. Modul akan ter-update otomatis

### Via Console:

```bash
php artisan modules:sync --no-interaction
```

## Hasil Setelah Sync

Setelah sync, tabel `modules` akan memiliki data:

```sql
SELECT id, name, code, slug, icon FROM modules;
```

| id  | name                | code                | slug                | icon                    |
| --- | ------------------- | ------------------- | ------------------- | ----------------------- |
| 1   | Manajemen Pengguna  | USER_MANAGEMENT     | user-management     | fa-users-cog            |
| 2   | Manajemen Aktivitas | ACTIVITY_MANAGEMENT | activity-management | fa-tasks                |
| 3   | Manajemen Risiko    | RISK_MANAGEMENT     | risk-management     | fa-exclamation-triangle |
| ... | ...                 | ...                 | ...                 | ...                     |

## Template module.json

Untuk modul baru, gunakan template berikut:

```json
{
  "name": "Nama Modul dalam Bahasa Indonesia",
  "alias": "nama-modul-kebab-case",
  "description": "Deskripsi modul dalam Bahasa Indonesia",
  "version": "1.0.0",
  "icon": "fa-icon-name",
  "keywords": ["keyword1", "keyword2", "kata-kunci-indonesia"],
  "active": 1,
  "order": 10,
  "providers": ["Modules\\NamaModul\\Providers\\NamaModulServiceProvider"],
  "aliases": {},
  "files": []
}
```

## Catatan Penting

1. ✅ **Slug tidak berubah** - untuk menjaga konsistensi routing
2. ✅ **Code auto-generated** - dari slug saat sync
3. ✅ **Icon menggunakan FontAwesome 5** - pastikan tersedia di project
4. ✅ **Nama ditampilkan di UI** - gunakan Bahasa Indonesia yang jelas
5. ✅ **Version standardisasi** - semua modul menggunakan 1.0.0

## Troubleshooting

### Icon tidak muncul?

- Pastikan FontAwesome 5 ter-load di layout
- Cek di `resources/views/layouts/app.blade.php`
- Verifikasi class icon: `<i class="fas fa-icon-name"></i>`

### Nama tidak berubah setelah sync?

- Sync hanya update `description` dan `icon`
- Untuk update `name`, perlu update manual di database atau hapus & re-sync

### Code conflict?

- System otomatis menambahkan suffix `_1`, `_2`, dst jika ada conflict
- Pastikan slug unique untuk menghindari conflict

## Referensi

- [FontAwesome 5 Icons](https://fontawesome.com/v5/search)
- [Module Structure Standards](./Module%20Development%20Guide/MODULE-STRUCTURE-STANDARDS.md)
- [Module Registration Guide](./Module%20Development%20Guide/MODULE-REGISTRATION-GUIDE.md)
