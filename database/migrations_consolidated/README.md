# Migrasi Database Terkonsolidasi

File-file dalam direktori ini merupakan versi terkonsolidasi dari seluruh migrasi yang ada di aplikasi SIAR. Tujuan dari konsolidasi ini adalah untuk mengurangi jumlah file migrasi dan mempermudah pemahaman struktur database secara keseluruhan.

## Daftar File Migrasi

1. **0001_create_base_tables.php**
   - Tabel-tabel dasar: users, tenants, modules, roles, work_units
   - Relasi antar tabel dasar: tenant_modules, role_module_permissions, tenant_module_configs

2. **0002_create_risk_management_module.php**
   - Tabel-tabel untuk modul manajemen risiko: risk_reports, risk_analysis

3. **0003_create_document_management_module.php**
   - Tabel-tabel untuk modul manajemen dokumen: documents, tags, document_tag, documentables, document_references

4. **0004_create_correspondence_module.php**
   - Tabel untuk modul korespondensi: correspondences

5. **0005_create_performance_management_module.php**
   - Tabel-tabel untuk modul manajemen kinerja: performance_indicators, performance_templates, performance_scores

6. **0006_create_activity_logs_table.php**
   - Tabel-tabel untuk pencatatan aktivitas: activity_log, activity_logs

7. **0007_create_telescope_entries_table.php**
   - Tabel-tabel untuk Laravel Telescope: telescope_entries, telescope_entries_tags, telescope_monitoring

## Cara Menggunakan

Untuk menggunakan migrasi terkonsolidasi ini, ikuti langkah-langkah berikut:

1. **Cadangkan database Anda** terlebih dahulu sebelum mengubah migrasi

2. **Migrasi pada lingkungan pengembangan baru:**
   - Salin semua file dari direktori `migrations_consolidated` ke direktori `migrations`
   - Hapus semua file migrasi yang lama (pastikan sudah dicadangkan)
   - Jalankan migrasi dengan perintah: `php artisan migrate`

3. **Migrasi pada lingkungan yang sudah ada:**
   - TIDAK DISARANKAN untuk mengganti migrasi yang sudah berjalan
   - Sebaiknya gunakan struktur ini untuk pengembangan atau deployment baru
   - Jika ingin menerapkan pada database yang sudah ada, gunakan `php artisan migrate:fresh` (akan menghapus semua data)

## Keuntungan Konsolidasi

1. **Jumlah file lebih sedikit** - Dari 60+ file menjadi hanya 7 file
2. **Pemahaman struktur lebih mudah** - Pengelompokan berdasarkan modul fungsional
3. **Migrasi lebih cepat** - Mengurangi jumlah operasi database saat menjalankan migrasi
4. **Memudahkan deployment** - Memudahkan proses deployment ke lingkungan baru

## Perhatian

Mengubah susunan migrasi yang sudah berjalan pada database produksi dapat menyebabkan masalah. Pendekatan ini lebih cocok untuk:

1. Proyek baru yang dimulai dari awal
2. Versi pra-rilis yang database produksinya belum ada
3. Referensi untuk memahami struktur database secara keseluruhan 