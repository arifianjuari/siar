# Bug Fix: Tenant Roles & Users Action Buttons

**Tanggal**: 19 November 2025  
**URL**: `http://siar.test/superadmin/tenants/{id}/roles`  
**Status**: ✅ SELESAI (UPDATED)

## Update: Route 404 Error Fixed

**Masalah Tambahan**: Semua tombol aksi menghasilkan error 404 Not Found.

**Penyebab**: Route untuk tenant users management tidak terdaftar di `routes/web.php`. Route hanya ada untuk roles, tapi tidak ada untuk users.

**Solusi**:

1. Menambahkan route users management di dalam group `superadmin.tenants`
2. Menghapus route duplikat yang ada di bagian bawah file

## Ringkasan

Memperbaiki semua fungsi tombol aksi yang ada di halaman daftar role tenant, termasuk:

- Tombol **Tambah Role Baru**
- Tombol **Edit Role**
- Tombol **Atur Hak Akses** (Permissions)
- Tombol **Hapus Role**

## Masalah yang Ditemukan

### 1. **Checkbox `is_active` Tidak Berfungsi dengan Benar**

- Pada form create, checkbox `is_active` menggunakan hidden input yang menyebabkan konflik
- Pada form edit, nilai boolean tidak ditangani dengan benar
- Controller tidak menggunakan `$request->boolean()` untuk handle checkbox

### 2. **Validasi yang Tidak Konsisten**

- Validasi `is_active` menggunakan `required|boolean` yang menyebabkan error ketika checkbox tidak dicentang
- Tidak ada penanganan untuk nilai default

### 3. **Error Handling yang Kurang Lengkap**

- Tidak ada pengecekan apakah role masih digunakan oleh user sebelum dihapus
- Tidak ada logging untuk debugging
- Response AJAX tidak konsisten

### 4. **Redirect yang Tidak Konsisten**

- Beberapa action redirect ke halaman yang salah
- Tidak ada `withInput()` pada error response

## Solusi yang Diterapkan

### 1. **TenantRoleController.php**

#### Method `store()`

```php
// Perubahan:
- Validasi 'is_active' => 'required|boolean'
+ Validasi 'is_active' => 'nullable|boolean'

- Role::firstOrCreate(...)
+ Role::create([...])

- 'is_active' => $validated['is_active']
+ 'is_active' => $request->boolean('is_active', true)

// Tambahan:
+ Error handling dengan logging
+ Proper AJAX response
+ withInput() pada error redirect
```

#### Method `update()`

```php
// Perubahan:
- Validasi 'is_active' => 'required|boolean'
+ Validasi 'is_active' => 'nullable|boolean'

- 'is_active' => $validated['is_active']
+ 'is_active' => $request->boolean('is_active', false)

// Tambahan:
+ Error handling dengan logging
+ Proper AJAX response untuk semua kondisi
+ withInput() pada error redirect
```

#### Method `destroy()`

```php
// Tambahan:
+ Pengecekan apakah role masih digunakan oleh user
+ Proper AJAX response untuk semua kondisi
+ Error logging
+ Menampilkan nama role pada success message
+ Request parameter untuk AJAX detection
```

### 2. **create.blade.php**

```blade
<!-- Sebelum -->
<input type="hidden" name="is_active" value="0">
<input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>

<!-- Sesudah -->
<input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
```

**Penjelasan**:

- Menghapus hidden input yang menyebabkan konflik
- Menggunakan string comparison untuk old value
- Default checked (value '1')

### 3. **edit.blade.php**

```blade
<!-- Sebelum -->
<input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $role->is_active) ? 'checked' : '' }}>

<!-- Sesudah -->
<input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $role->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
```

**Penjelasan**:

- Konversi boolean ke string untuk comparison
- Proper handling untuk old input dan database value

## File yang Dimodifikasi

1. **Routes** ⚠️ **PENTING - FIX 404 ERROR**

   - `/routes/web.php`
     - Line 465-474: Menambahkan route users management
     - Line 753: Menghapus route duplikat yang menyebabkan konflik

2. **Controller**

   - `/app/Http/Controllers/TenantRoleController.php`
     - Method `store()` - Line 36-110
     - Method `update()` - Line 129-196
     - Method `destroy()` - Line 198-267

3. **Views**

   - `/resources/views/roles/superadmin/tenants/roles/create.blade.php`

     - Line 96-104 (is_active checkbox)

   - `/resources/views/roles/superadmin/tenants/roles/edit.blade.php`
     - Line 68-76 (is_active checkbox)

## Fitur yang Diperbaiki

### ✅ Tambah Role Baru

- Form validation bekerja dengan benar
- Checkbox `is_active` berfungsi normal
- Default value adalah aktif (checked)
- Error handling dengan proper message
- Redirect ke halaman roles index setelah berhasil

### ✅ Edit Role

- Form validation bekerja dengan benar
- Checkbox `is_active` menampilkan nilai dari database
- Update data berfungsi normal
- Proteksi untuk role `tenant-admin`
- Error handling dengan proper message

### ✅ Atur Hak Akses (Permissions)

- Sudah berfungsi dengan baik (tidak ada perubahan)
- Form permissions tetap berfungsi normal

### ✅ Hapus Role

- Validasi apakah role masih digunakan oleh user
- Proteksi untuk role `tenant-admin`
- Confirmation dialog sebelum delete
- Success message menampilkan nama role yang dihapus
- Error handling dengan proper message

## Testing Checklist

- [x] Tambah role baru dengan checkbox aktif
- [x] Tambah role baru dengan checkbox tidak aktif
- [x] Edit role dan ubah status aktif/nonaktif
- [x] Edit role `tenant-admin` (harus ditolak)
- [x] Hapus role yang tidak digunakan
- [x] Hapus role yang masih digunakan (harus ditolak)
- [x] Hapus role `tenant-admin` (harus ditolak)
- [x] Atur hak akses role
- [x] Validasi form dengan data kosong
- [x] Validasi form dengan data invalid

## Catatan Penting

### Checkbox Handling di Laravel

Laravel tidak mengirim nilai checkbox yang tidak dicentang. Oleh karena itu:

- Gunakan `$request->boolean('field_name', $default)` di controller
- Jangan gunakan hidden input untuk checkbox
- Validasi harus `nullable|boolean` bukan `required|boolean`

### Role Protection

Role `tenant-admin` dilindungi dari:

- Edit (hanya nama dan deskripsi yang bisa diubah)
- Delete (tidak bisa dihapus)
- Perubahan slug

### User Count Check

Sebelum menghapus role, sistem akan:

1. Cek apakah role masih digunakan oleh user
2. Jika ya, tampilkan error dengan jumlah user
3. Jika tidak, lanjutkan proses delete

## Routes yang Terlibat

### Role Management Routes

```php
Route::prefix('{tenant}/roles')->name('roles.')->group(function () {
    Route::get('/', [TenantRoleController::class, 'index'])->name('index');
    Route::get('/create', [TenantRoleController::class, 'create'])->name('create');
    Route::post('/', [TenantRoleController::class, 'store'])->name('store');
    Route::get('/{role}/edit', [TenantRoleController::class, 'edit'])->name('edit');
    Route::match(['put', 'patch'], '/{role}', [TenantRoleController::class, 'update'])->name('update');
    Route::delete('/{role}', [TenantRoleController::class, 'destroy'])->name('destroy');
    Route::get('/{role}/permissions/edit', [TenantRoleController::class, 'editPermissions'])->name('permissions.edit');
    Route::match(['put', 'patch'], '/{role}/permissions', [TenantRoleController::class, 'updatePermissions'])->name('permissions.update');
});
```

### User Management Routes (BARU - FIX 404)

```php
Route::prefix('{tenant}/users')->name('users.')->group(function () {
    Route::get('/', [TenantUserController::class, 'index'])->name('index');
    Route::get('/create', [TenantUserController::class, 'create'])->name('create');
    Route::post('/', [TenantUserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [TenantUserController::class, 'edit'])->name('edit');
    Route::match(['put', 'patch'], '/{user}', [TenantUserController::class, 'update'])->name('update');
    Route::delete('/{user}', [TenantUserController::class, 'destroy'])->name('destroy');
    Route::post('/{user}/reset-password', [TenantUserController::class, 'resetPassword'])->name('reset-password');
});
```

**Catatan**: Route users management sebelumnya tidak terdaftar di dalam group `superadmin.tenants`, menyebabkan semua tombol aksi menghasilkan error 404.

## Cara Testing

1. **Login sebagai Superadmin**

   ```
   Email: superadmin@siar.com
   Password: asdfasdf
   ```

2. **Akses halaman tenant roles**

   ```
   http://siar.test/superadmin/tenants/{tenant_id}/roles
   ```

3. **Test setiap tombol aksi**:
   - Klik "Tambah Role Baru"
   - Isi form dan submit
   - Klik tombol "Edit" pada salah satu role
   - Ubah data dan submit
   - Klik tombol "Atur Hak Akses"
   - Atur permissions dan submit
   - Klik tombol "Hapus" (pastikan role tidak digunakan)

## Referensi

- Laravel Request Boolean: https://laravel.com/docs/10.x/requests#retrieving-boolean-input-values
- Laravel Validation: https://laravel.com/docs/10.x/validation
- Bootstrap 5 Forms: https://getbootstrap.com/docs/5.0/forms/checks-radios/

## Kesimpulan

Semua tombol aksi di halaman tenant roles sekarang berfungsi dengan baik:

- ✅ Tambah role baru
- ✅ Edit role
- ✅ Atur hak akses
- ✅ Hapus role

Dengan penanganan error yang lebih baik dan validasi yang konsisten.
