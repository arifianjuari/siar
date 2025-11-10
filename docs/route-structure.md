# Struktur Route SIAR

Dokumen ini menjelaskan struktur route aplikasi SIAR untuk mencegah duplicate route names.

## Prinsip Utama

1. **Satu Route, Satu Definisi**: Setiap route hanya boleh didefinisikan sekali
2. **Modular Routes**: Route untuk setiap modul didefinisikan di file terpisah di `routes/modules/`
3. **Konsistensi Prefix**: Semua route modul menggunakan prefix `modules/{module-name}`

## Struktur File Route

```
routes/
├── web.php                    # Route utama aplikasi
├── api.php                    # Route API
├── auth.php                   # Route autentikasi
└── modules/
    ├── ActivityManagement.php
    ├── Correspondence.php
    ├── UserManagement.php
    ├── WorkUnit.php
    └── KendaliMutuBiaya.php
```

## Konvensi Penamaan Route

### Route Modul

Semua route modul harus mengikuti pola berikut:

```php
Route::middleware(['web', 'auth', 'tenant', 'module:{module-name}'])
    ->prefix('modules/{module-name}')
    ->name('modules.{module-name}.')
    ->group(function () {
        // Route definitions
    });
```

**Contoh:**
```php
// routes/modules/UserManagement.php
Route::middleware(['web', 'auth', 'tenant', 'module:user-management'])
    ->prefix('modules/user-management')
    ->name('modules.user-management.')
    ->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
    });
```

### Route Name Pattern

- **Modul Route**: `modules.{module-name}.{resource}.{action}`
- **Contoh**: `modules.user-management.users.index`

## File Route yang Di-require

File `routes/web.php` me-require semua file modul di bagian akhir:

```php
require __DIR__ . '/modules/ActivityManagement.php';
require __DIR__ . '/modules/WorkUnit.php';
require __DIR__ . '/modules/Correspondence.php';
require __DIR__ . '/modules/UserManagement.php';
require __DIR__ . '/modules/KendaliMutuBiaya.php';
```

## ⚠️ PENTING: Jangan Duplikat Route

### ❌ SALAH - Jangan lakukan ini:

```php
// routes/web.php
Route::prefix('modules')->name('modules.')->group(function () {
    Route::prefix('user-management')->name('user-management.')->group(function () {
        Route::resource('users', UserController::class); // ❌ DUPLICATE!
    });
});

// routes/modules/UserManagement.php
Route::prefix('modules/user-management')->name('modules.user-management.')->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index'); // ❌ DUPLICATE!
});
```

### ✅ BENAR - Lakukan ini:

```php
// routes/web.php
// Jangan definisikan route modul di sini jika sudah ada di file modul terpisah

// routes/modules/UserManagement.php
Route::middleware(['web', 'auth', 'tenant', 'module:user-management'])
    ->prefix('modules/user-management')
    ->name('modules.user-management.')
    ->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
    });
```

## Checklist Sebelum Menambah Route Baru

- [ ] Cek apakah route sudah ada di file modul terpisah
- [ ] Pastikan route name unik (tidak duplicate)
- [ ] Gunakan prefix yang konsisten (`modules/{module-name}`)
- [ ] Test dengan `php artisan route:cache` untuk memastikan tidak ada error
- [ ] Verifikasi dengan `php artisan route:list --name={route-name}` untuk memastikan hanya ada satu route

## Cara Mengecek Duplicate Route

### 1. Test Route Cache

```bash
php artisan route:clear
php artisan route:cache
```

Jika ada duplicate, akan muncul error:
```
Unable to prepare route [...] for serialization. 
Another route has already been assigned name [...]
```

### 2. List Route dengan Name

```bash
php artisan route:list --name={route-name}
```

Jika muncul lebih dari satu route dengan name yang sama, berarti ada duplicate.

### 3. Search Route Name di Codebase

```bash
grep -r "->name('{route-name}')" routes/
```

## Route yang Sudah Diperbaiki

### 1. User Management
- **Masalah**: Route didefinisikan di `web.php` dan `UserManagement.php`
- **Solusi**: Hapus route dari `web.php`, gunakan hanya `UserManagement.php`

### 2. Correspondence
- **Masalah**: Route didefinisikan di `web.php` dan `Correspondence.php`
- **Solusi**: Hapus route dari `web.php`, update prefix di `Correspondence.php` menjadi `modules/correspondence`

### 3. Activity Management
- **Masalah**: Duplicate route `assignees.store` dan `assignees.destroy`
- **Solusi**: Hapus route duplicate, gunakan route dengan middleware permission check

### 4. Document Management
- **Masalah**: Duplicate route `documents.edit`
- **Solusi**: Exclude `edit` dan `update` dari resource route, tambahkan custom route dengan middleware

### 5. Tags
- **Masalah**: Duplicate route `tags.attach-document` (POST dan DELETE)
- **Solusi**: Rename DELETE route menjadi `tags.detach-document`

## Best Practices

1. **Gunakan File Terpisah**: Setiap modul harus memiliki file route terpisah
2. **Konsisten Prefix**: Gunakan prefix `modules/{module-name}` untuk semua route modul
3. **Test Route Cache**: Selalu test dengan `php artisan route:cache` sebelum commit
4. **Dokumentasi**: Dokumentasikan route baru di file ini jika perlu
5. **Code Review**: Review route changes untuk memastikan tidak ada duplicate

## Troubleshooting

### Error: "Another route has already been assigned name"

1. Cari route name yang duplicate:
   ```bash
   grep -r "->name('{route-name}')" routes/
   ```

2. Hapus salah satu definisi route (pilih yang lebih lengkap dengan middleware)

3. Test lagi:
   ```bash
   php artisan route:clear
   php artisan route:cache
   ```

### Error: Route tidak ditemukan

1. Pastikan file modul di-require di `routes/web.php`
2. Cek prefix dan name route sudah benar
3. Clear route cache:
   ```bash
   php artisan route:clear
   ```

## Referensi

- [Laravel Routing Documentation](https://laravel.com/docs/routing)
- [Route Caching](https://laravel.com/docs/routing#route-caching)

