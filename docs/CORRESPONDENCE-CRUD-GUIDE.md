# Panduan Fitur CRUD Correspondence Management

## Status Implementasi ✅

Modul **Correspondence Management** sudah memiliki fitur CRUD lengkap dan siap digunakan!

## Fitur yang Tersedia

### 1. **Dashboard**

URL: `https://siar-beta-ctegvo.laravel.cloud/modules/correspondence/dashboard`

**Fitur:**

- ✅ Statistik total surat & nota dinas
- ✅ Grafik tren 12 bulan terakhir
- ✅ Distribusi jenis dokumen (Regulasi vs Bukti)
- ✅ Aktivitas terbaru
- ✅ **Tombol Quick Actions:**
  - Lihat Semua Surat
  - Buat Surat Baru

### 2. **List/Index - Daftar Surat**

URL: `https://siar-beta-ctegvo.laravel.cloud/modules/correspondence/letters`

**Fitur:**

- ✅ Tabel daftar semua surat/nota dinas
- ✅ Filter berdasarkan:
  - Tag
  - Tipe Dokumen (Regulasi/Bukti)
  - Tingkat Kerahasiaan
  - Pencarian (judul, nomor, perihal, pengirim, penerima)
- ✅ Pagination (10 items per halaman)
- ✅ Tombol aksi: View, Edit, Delete
- ✅ Tombol "Buat Surat Baru"

### 3. **Create - Buat Surat Baru**

URL: `https://siar-beta-ctegvo.laravel.cloud/modules/correspondence/letters/create`

**Form Fields:**

- Informasi Dokumen:

  - Judul Dokumen \*
  - Nomor Dokumen
  - Tipe Dokumen (Regulasi/Bukti) \*
  - Versi Dokumen \*
  - Tanggal Dokumen \*
  - Tingkat Kerahasiaan (Internal/Publik/Rahasia) \*

- Isi Surat:

  - Perihal/Subject \*
  - Isi Surat/Body \*
  - Referensi/Merujuk Ke

- Pengirim & Penerima:

  - Nama Pengirim \*
  - Jabatan Pengirim \*
  - Nama Penerima \*
  - Jabatan Penerima \*
  - CC List (Tembusan)

- Penandatangan:

  - Tempat Penandatanganan \*
  - Tanggal Penandatanganan \*
  - Nama Penandatangan \*
  - Jabatan Penandatangan \*
  - Pangkat Penandatangan
  - NRP/NIK Penandatangan

- File Upload:

  - File Dokumen (PDF, DOC, DOCX, max 10MB)
  - File Tanda Tangan (PNG, JPG, JPEG, max 2MB)
  - Link Dokumen Eksternal

- Tags:
  - Pilih atau buat tag baru

**Validasi:**

- Semua field required (bertanda \*) wajib diisi
- Format file sesuai ketentuan
- Size file tidak melebihi batas

### 4. **Read/Show - Lihat Detail Surat**

URL: `https://siar-beta-ctegvo.laravel.cloud/modules/correspondence/letters/{id}`

**Fitur:**

- ✅ Tampilan lengkap semua informasi surat
- ✅ Tags yang terkait
- ✅ File attachment (jika ada)
- ✅ QR Code surat
- ✅ Tombol aksi:
  - Edit
  - Hapus
  - Export PDF
  - Export Word
  - Generate QR Code

### 5. **Update/Edit - Edit Surat**

URL: `https://siar-beta-ctegvo.laravel.cloud/modules/correspondence/letters/{id}/edit`

**Fitur:**

- ✅ Form pre-filled dengan data existing
- ✅ Upload file baru (replace file lama)
- ✅ Update tags
- ✅ Update semua field

### 6. **Delete - Hapus Surat**

**Fitur:**

- ✅ Hapus surat dari database
- ✅ Hapus file terkait (dokumen & signature)
- ✅ Hapus relasi tags & documents
- ✅ Redirect ke list dengan pesan sukses

### 7. **Export Features**

**Export PDF:**

- URL: `/modules/correspondence/letters/{id}/export-pdf`
- Format: PDF A4 Portrait
- Include: QR Code

**Export Word:**

- URL: `/modules/correspondence/letters/{id}/export-word`
- Format: DOCX
- Menggunakan Pandoc converter

**Generate QR Code:**

- URL: `/modules/correspondence/letters/{id}/qr-code`
- Format: SVG
- Content: Nomor, Tanggal, Perihal, Penandatangan, URL

### 8. **Search & Filter**

URL: `https://siar-beta-ctegvo.laravel.cloud/modules/correspondence/search`

**Parameter:**

- `tag`: Filter by tag slug
- `document_type`: Regulasi/Bukti
- `confidentiality_level`: Internal/Publik/Rahasia
- `search`: Free text search

## Permissions/Izin

Module menggunakan permission checking:

- `can_view`: Melihat daftar & detail surat
- `can_create`: Membuat surat baru
- `can_edit`: Mengedit surat
- `can_delete`: Menghapus surat
- `can_export`: Export PDF/Word

## Routes Tersedia

```php
// Dashboard
GET /modules/correspondence/dashboard

// CRUD Letters
GET    /modules/correspondence/letters           (index)
GET    /modules/correspondence/letters/create    (create)
POST   /modules/correspondence/letters           (store)
GET    /modules/correspondence/letters/{id}      (show)
GET    /modules/correspondence/letters/{id}/edit (edit)
PUT    /modules/correspondence/letters/{id}      (update)
DELETE /modules/correspondence/letters/{id}      (destroy)

// Export
GET /modules/correspondence/letters/{id}/export-pdf
GET /modules/correspondence/letters/{id}/export-word
GET /modules/correspondence/letters/{id}/qr-code
GET /modules/correspondence/letters/{id}/qr-code-base64

// Search
GET /modules/correspondence/search
```

## Cara Menggunakan

### 1. **Akses Dashboard**

```
https://siar-beta-ctegvo.laravel.cloud/modules/correspondence/dashboard
```

### 2. **Klik "Buat Surat Baru"**

- Tombol hijau di header dashboard
- Atau dari menu Quick Actions

### 3. **Isi Formulir**

- Lengkapi semua field required (bertanda \*)
- Upload file jika diperlukan
- Pilih/tambah tags

### 4. **Simpan**

- Klik tombol "Simpan" di bawah form
- Anda akan diredirect ke halaman detail surat

### 5. **Lihat Daftar Surat**

- Klik "Lihat Semua Surat" dari dashboard
- Atau akses langsung: `/modules/correspondence/letters`

### 6. **Edit Surat**

- Dari detail page, klik tombol "Edit"
- Atau dari list page, klik icon edit

### 7. **Hapus Surat**

- Dari detail page, klik tombol "Hapus"
- Konfirmasi penghapusan
- Surat akan dihapus permanen

### 8. **Export Surat**

- Dari detail page, pilih:
  - Export PDF
  - Export Word
  - Generate QR Code

## Update yang Dilakukan

### 1. **CorrespondenceController.php**

- ✅ Update method `dashboard()` untuk mengirim data lengkap:
  - Stats (total, thisMonth, draft, pending_review, regulasi, bukti)
  - Chart data (monthly & status distribution)
  - Recent letters untuk aktivitas terbaru

### 2. **dashboard.blade.php**

- ✅ Tambah tombol CRUD di header dashboard:
  - "Lihat Semua Surat" (Primary button)
  - "Buat Surat Baru" (Success button)
- ✅ Tombol menggunakan permission checking
- ✅ Quick Actions section sudah ada di bawah

## Database Tables

**Table: `correspondences`**

- Menyimpan semua data surat/nota dinas
- Foreign key: `tenant_id`, `created_by`
- File paths: `file_path`, `signature_file`

**Table: `document_tag`**

- Polymorphic many-to-many relation
- Menghubungkan correspondence dengan tags

## Testing Checklist

- [ ] Dashboard tampil dengan benar
- [ ] Tombol "Lihat Semua Surat" berfungsi
- [ ] Tombol "Buat Surat Baru" berfungsi
- [ ] Form create bisa diisi dan disimpan
- [ ] List surat tampil dengan data
- [ ] Detail surat dapat dibuka
- [ ] Edit surat berfungsi
- [ ] Hapus surat berfungsi
- [ ] Export PDF berfungsi
- [ ] Export Word berfungsi
- [ ] QR Code berfungsi
- [ ] Search & filter berfungsi

## Troubleshooting

### Jika tombol tidak muncul:

1. Cek permission user untuk module correspondence-management
2. Pastikan user memiliki izin `can_create`, `can_edit`, dll
3. Clear cache: `php artisan cache:clear`

### Jika upload file gagal:

1. Cek folder `storage/app/public/correspondences` ada
2. Cek folder `storage/app/public/signatures` ada
3. Pastikan symlink storage sudah dibuat
4. Cek permission folder (775 atau 777)

### Jika QR Code tidak muncul:

1. Pastikan package `simplesoftwareio/simple-qrcode` terinstall
2. Check routes: `/modules/correspondence/letters/{id}/qr-code`

## Kesimpulan

**Semua fitur CRUD sudah tersedia dan siap digunakan!** 🎉

Anda dapat langsung:

1. Membuat surat baru
2. Melihat daftar surat
3. Mengedit surat existing
4. Menghapus surat
5. Export ke PDF/Word
6. Generate QR Code

Tidak perlu development tambahan untuk fitur CRUD dasar.
