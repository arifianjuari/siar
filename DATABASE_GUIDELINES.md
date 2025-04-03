# Panduan Perubahan Database SIAR

Dokumen ini menyediakan prosedur dan praktik terbaik untuk melakukan perubahan pada database aplikasi SIAR. Tujuan utama adalah memastikan perubahan database dilakukan dengan aman tanpa menyebabkan kehilangan data.

## Prinsip Utama

1. **Tidak ada DROP TABLE di Production**: Jangan pernah menghapus tabel di lingkungan produksi
2. **Tidak ada migrate:fresh**: Perintah ini menghapus semua tabel dan datanya
3. **Tidak ada migrate:refresh**: Perintah ini melakukan rollback dan menghapus data
4. **Migrasi selalu maju, tidak mundur**: Gunakan migrasi tambahan untuk memodifikasi struktur

## Prosedur Perubahan Database

### 1. Perubahan yang Diperbolehkan

- ✅ Menambahkan tabel baru
- ✅ Menambahkan kolom baru (nullable atau dengan default value)
- ✅ Menambahkan indeks baru
- ✅ Menambahkan foreign key baru (dengan nullOnDelete)
- ✅ Memodifikasi enum dengan menambahkan nilai baru

### 2. Perubahan yang Memerlukan Kehati-hatian

- ⚠️ Mengubah tipe data kolom
- ⚠️ Mengganti nama tabel
- ⚠️ Mengganti nama kolom
- ⚠️ Menghapus indeks

### 3. Perubahan yang Terlarang

- ❌ Menghapus tabel
- ❌ Menghapus kolom
- ❌ Mengubah primary key

## Praktik Terbaik untuk Migrasi

### 1. Membuat Migrasi Baru

```bash
php artisan make:migration nama_migrasi_deskriptif
```

### 2. Format Migrasi

Untuk menambahkan kolom:

```php
Schema::table('nama_tabel', function (Blueprint $table) {
    $table->string('kolom_baru')->nullable()->after('kolom_sebelumnya');
});
```

Untuk mengganti nama kolom (gunakan nama tabel dan kolom yang benar):

```php
Schema::table('nama_tabel', function (Blueprint $table) {
    $table->renameColumn('nama_lama', 'nama_baru');
});
```

### 3. Foreign Keys Selalu dengan nullOnDelete()

```php
$table->foreignId('related_id')->nullable()->constrained()->nullOnDelete();
```

### 4. Selalu Buat Metode down()

```php
public function down()
{
    Schema::table('nama_tabel', function (Blueprint $table) {
        $table->dropColumn('kolom_baru');
    });
}
```

## Prosedur Deployment

1. **Backup Database**: Sebelum melakukan perubahan apapun
   ```bash
   php artisan db:backup --compress
   ```

2. **Jalankan Migrasi dalam Mode Aman**:
   ```bash
   php artisan migrate --pretend  # Cek apa yang akan dilakukan
   php artisan migrate --force    # Jalankan migrasi dengan force
   ```

3. **Validasi**: Periksa perubahan setelah migrasi

4. **Rollback hanya untuk migrasi terakhir**: Jika ada masalah
   ```bash
   php artisan migrate:rollback --step=1
   ```

## Penanganan Kesalahan

Jika terjadi kesalahan saat menjalankan migrasi:

1. **Jangan Panik**: Tenang dan analisis kesalahan
2. **Buat Migrasi Baru**: Untuk memperbaiki masalah
3. **Gunakan Query Raw**: Jika diperlukan untuk kasus kompleks
4. **Konsultasi dengan Tim**: Diskusikan perubahan besar dengan tim

---

Dokumen ini dibuat untuk keamanan data aplikasi SIAR. Selalu ikuti prosedur ini untuk menghindari hilangnya data. 