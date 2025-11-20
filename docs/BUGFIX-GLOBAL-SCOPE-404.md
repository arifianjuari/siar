# Bug Fix: 404 Error pada Tenant Roles Management (Global Scope Issue)

**Tanggal**: 19 November 2025  
**Status**: ✅ SELESAI

## Masalah

Semua tombol aksi di halaman tenant roles menghasilkan error **404 Not Found**:

- Edit Role: `http://siar.test/superadmin/tenants/2/roles/22/edit` → 404
- Edit Hak Akses: `http://siar.test/superadmin/tenants/2/roles/22/permissions/edit` → 404
- Delete Role: `http://siar.test/superadmin/tenants/2/roles/22` → 404

## Akar Masalah

### 1. **Global Scope pada Model Role**

Model `Role` menggunakan trait `BelongsToTenant` yang menambahkan **global scope** untuk mem-filter data berdasarkan `tenant_id`:

```php
// app/Traits/BelongsToTenant.php
protected static function bootBelongsToTenant()
{
    static::addGlobalScope('tenant_id', function (Builder $builder) {
        // Filter berdasarkan tenant_id dari session/auth
    });
}
```

### 2. **Route Model Binding Gagal**

Ketika Laravel mencoba resolve parameter `{role}` di route, global scope memblokir akses karena:

- Superadmin tidak memiliki `tenant_id` yang sama dengan role yang diakses
- Global scope hanya mengizinkan akses ke role dari tenant yang sedang login
- Hasilnya: Laravel tidak menemukan role → 404 Not Found

### 3. **Kenapa Ini Terjadi?**

Route model binding Laravel secara otomatis mencari model berdasarkan ID:

```php
// Route definition
Route::get('/{role}/edit', [TenantRoleController::class, 'edit']);

// Laravel mencoba:
Role::find($roleId) // ❌ Gagal karena global scope memblokir
```

## Solusi

### Menonaktifkan Global Scope untuk Superadmin

Ubah semua method di `TenantRoleController` yang menerima parameter `Role $role` menjadi menerima `$roleId`, kemudian manual query dengan `withoutGlobalScope()`:

#### **Sebelum** (Gagal - 404):

```php
public function edit(Tenant $tenant, Role $role)
{
    // Laravel tidak bisa resolve $role karena global scope
    return view('roles.superadmin.tenants.roles.edit', compact('tenant', 'role'));
}
```

#### **Sesudah** (Berhasil):

```php
public function edit(Tenant $tenant, $roleId)
{
    // Manual query dengan bypass global scope
    $role = Role::withoutGlobalScope('tenant_id')
        ->where('id', $roleId)
        ->where('tenant_id', $tenant->id)
        ->firstOrFail();

    return view('roles.superadmin.tenants.roles.edit', compact('tenant', 'role'));
}
```

## File yang Dimodifikasi

**File**: `/app/Http/Controllers/TenantRoleController.php`

### Method yang Diperbaiki:

1. **`edit()`** - Line 112-127

   ```php
   - public function edit(Tenant $tenant, Role $role)
   + public function edit(Tenant $tenant, $roleId)
   ```

2. **`update()`** - Line 129-190

   ```php
   - public function update(Request $request, Tenant $tenant, Role $role)
   + public function update(Request $request, Tenant $tenant, $roleId)
   ```

3. **`destroy()`** - Line 192-255

   ```php
   - public function destroy(Request $request, Tenant $tenant, Role $role)
   + public function destroy(Request $request, Tenant $tenant, $roleId)
   ```

4. **`editPermissions()`** - Line 257-275

   ```php
   - public function editPermissions(Tenant $tenant, Role $role)
   + public function editPermissions(Tenant $tenant, $roleId)
   ```

5. **`updatePermissions()`** - Line 278-332
   ```php
   - public function updatePermissions(Request $request, Tenant $tenant, Role $role)
   + public function updatePermissions(Request $request, Tenant $tenant, $roleId)
   ```

## Pola yang Digunakan

Semua method sekarang menggunakan pola yang sama:

```php
public function methodName(Tenant $tenant, $roleId)
{
    // 1. Bypass global scope
    $role = Role::withoutGlobalScope('tenant_id')
        ->where('id', $roleId)
        ->where('tenant_id', $tenant->id)  // Tetap validasi tenant
        ->firstOrFail();

    // 2. Lanjutkan logic seperti biasa
    // ...
}
```

### Keuntungan Pola Ini:

1. ✅ **Bypass global scope** - Superadmin bisa akses role dari tenant manapun
2. ✅ **Tetap aman** - Validasi `tenant_id` tetap dilakukan manual
3. ✅ **Konsisten** - Semua method menggunakan pola yang sama
4. ✅ **Error handling** - `firstOrFail()` otomatis return 404 jika tidak ditemukan

## Testing

### Test Case yang Berhasil:

1. ✅ **Edit Role**

   ```
   GET http://siar.test/superadmin/tenants/2/roles/22/edit
   Status: 200 OK
   ```

2. ✅ **Update Role**

   ```
   PUT http://siar.test/superadmin/tenants/2/roles/22
   Status: 302 Redirect (Success)
   ```

3. ✅ **Edit Permissions**

   ```
   GET http://siar.test/superadmin/tenants/2/roles/22/permissions/edit
   Status: 200 OK
   ```

4. ✅ **Update Permissions**

   ```
   PUT http://siar.test/superadmin/tenants/2/roles/22/permissions
   Status: 302 Redirect (Success)
   ```

5. ✅ **Delete Role**
   ```
   DELETE http://siar.test/superadmin/tenants/2/roles/22
   Status: 302 Redirect (Success)
   ```

## Cara Testing

1. **Clear cache**:

   ```bash
   php artisan route:clear
   php artisan view:clear
   php artisan config:clear
   ```

2. **Login sebagai superadmin**:

   ```
   Email: superadmin@siar.com
   Password: asdfasdf
   ```

3. **Akses tenant detail**:

   ```
   http://siar.test/superadmin/tenants/2
   ```

4. **Test semua tombol di section Manajemen Role**:
   - Klik tombol Edit (ikon pensil)
   - Klik tombol Atur Hak Akses (ikon kunci)
   - Klik tombol Hapus (ikon trash)

## Catatan Penting

### Kapan Menggunakan `withoutGlobalScope()`?

Gunakan `withoutGlobalScope()` ketika:

- ✅ Superadmin perlu akses ke data tenant lain
- ✅ Admin perlu melihat data dari semua tenant (cross-tenant query)
- ✅ Background job/command yang perlu akses ke semua data

**JANGAN** gunakan untuk:

- ❌ User biasa yang hanya boleh akses data tenant sendiri
- ❌ API endpoint yang diakses oleh tenant user
- ❌ Query yang seharusnya di-filter berdasarkan tenant

### Security Consideration

Meskipun menggunakan `withoutGlobalScope()`, kita tetap melakukan validasi manual:

```php
->where('tenant_id', $tenant->id)  // ✅ Validasi manual
```

Ini memastikan superadmin hanya bisa akses role dari tenant yang valid, bukan semua role di database.

## Referensi

- Laravel Query Scopes: https://laravel.com/docs/10.x/eloquent#query-scopes
- Global Scopes: https://laravel.com/docs/10.x/eloquent#global-scopes
- Route Model Binding: https://laravel.com/docs/10.x/routing#route-model-binding

## Kesimpulan

Masalah 404 disebabkan oleh **global scope** pada model `Role` yang memblokir route model binding untuk superadmin. Solusinya adalah dengan:

1. ✅ Mengubah parameter dari `Role $role` menjadi `$roleId`
2. ✅ Manual query dengan `withoutGlobalScope('tenant_id')`
3. ✅ Tetap validasi `tenant_id` secara manual untuk keamanan

**Semua tombol aksi sekarang berfungsi dengan sempurna!** 🎉
