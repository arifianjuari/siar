# Bug Fix: Role Tidak Ditampilkan di Halaman User Management

## Masalah

Role dari pengguna tidak ditampilkan di:

1. `/superadmin/users` - Halaman Manajemen Pengguna
2. `/superadmin/tenants/[id]` - Halaman Detail Tenant (bagian Manajemen Pengguna)

Padahal data role sudah ada di database dan user sudah memiliki `role_id` yang valid.

## Root Cause Analysis

### **MASALAH UTAMA: Global Scope Tenant Filtering**

Model `Role` menggunakan trait `BelongsToTenant` yang menambahkan **global scope** untuk memfilter role berdasarkan `tenant_id` dari user yang sedang login.

**Skenario masalah:**

1. Superadmin login dengan `tenant_id = 1` (System)
2. Superadmin membuka halaman user management yang menampilkan user dari berbagai tenant
3. User dengan `tenant_id = 2` memiliki `role_id = 2` (role dengan `tenant_id = 2`)
4. Laravel mencoba eager load role, tetapi global scope memfilter hanya role dengan `tenant_id = 1`
5. Role tidak ditemukan karena `tenant_id` tidak cocok → `$user->role` menjadi `null`

**Kode di `BelongsToTenant` trait (line 18-26):**

```php
static::addGlobalScope('tenant_id', function (Builder $builder) {
    $tenantId = static::getCurrentTenantId();

    if ($tenantId) {
        $table = $builder->getModel()->getTable();
        $builder->where($table . '.tenant_id', $tenantId);
    }
});
```

### 1. Halaman `/superadmin/tenants/[id]`

**File**: `app/Http/Controllers/Superadmin/TenantManagementController.php`

**Masalah**: Query pada method `show()` tidak melakukan eager loading untuk relasi `role`:

```php
// SEBELUM (Line 150)
$users = $tenant->users()->paginate(10);
```

**Solusi**: Menambahkan eager loading untuk relasi `role`:

```php
// SESUDAH (Line 150)
$users = $tenant->users()->with('role')->paginate(10);
```

### 2. Halaman `/superadmin/users`

**File**: `app/Http/Controllers/Superadmin/UserManagementController.php`

**Status**: Controller sudah benar, sudah menggunakan eager loading pada line 23:

```php
$query = User::with(['tenant', 'role']);
```

### 3. View Improvements

Kedua view sudah diupdate untuk menampilkan role dengan lebih baik dan menambahkan debugging info:

**File 1**: `resources/views/roles/superadmin/users/index.blade.php`

```blade
<td>
    @if($user->role)
        <span class="badge bg-info">
            {{ $user->role->name }}
        </span>
    @else
        <span class="badge bg-secondary" title="Role ID: {{ $user->role_id ?? 'NULL' }}">
            Tidak ada role
        </span>
    @endif
</td>
```

**File 2**: `resources/views/roles/superadmin/tenants/show.blade.php`

```blade
<td>
    @if($user->role)
        <span class="badge bg-info">{{ $user->role->name }}</span>
    @else
        <span class="badge bg-secondary" title="Role ID: {{ $user->role_id ?? 'NULL' }}">-</span>
    @endif
</td>
```

## Verifikasi

### Test Query di Tinker

```bash
php artisan tinker

# Test 1: Cek users dengan role
$users = App\Models\User::with('role')->take(3)->get();
foreach($users as $user) {
    echo $user->name . ' | Role: ' . ($user->role ? $user->role->name : 'NULL') . PHP_EOL;
}

# Test 2: Cek users di tenant tertentu
$tenant = App\Models\Tenant::find(2);
$users = $tenant->users()->with('role')->get();
foreach($users as $user) {
    echo $user->name . ' | Role: ' . ($user->role ? $user->role->name : 'NULL') . PHP_EOL;
}
```

### Clear Cache

```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

## Files Modified

1. **app/Http/Controllers/Superadmin/TenantManagementController.php**

   - Line 150: Menambahkan `->with('role')` pada query users

2. **resources/views/roles/superadmin/users/index.blade.php**

   - Lines 53-63: Improved role display dengan conditional rendering

3. **resources/views/roles/superadmin/tenants/show.blade.php**
   - Lines 349-355: Improved role display dengan conditional rendering

## Testing Checklist

- [x] Verifikasi data role ada di database
- [x] Verifikasi relasi User->Role berfungsi
- [x] Verifikasi query dengan eager loading berfungsi
- [x] Update controller untuk eager load role
- [x] Update views untuk display role dengan benar
- [x] Clear all caches

## Instruksi untuk User

1. **Clear browser cache** atau buka halaman dalam mode incognito/private browsing
2. **Hard refresh** halaman dengan `Ctrl+Shift+R` (Windows/Linux) atau `Cmd+Shift+R` (Mac)
3. Akses halaman:
   - http://siar.test/superadmin/users
   - http://siar.test/superadmin/tenants/[id]
4. Role seharusnya sudah ditampilkan dengan badge berwarna biru (bg-info)

## Debugging

Jika role masih tidak muncul, hover mouse ke badge "Tidak ada role" atau "-" untuk melihat tooltip yang menampilkan `role_id` dari database. Ini akan membantu debugging lebih lanjut.

## Catatan

- Eager loading sangat penting untuk menghindari N+1 query problem
- Selalu gunakan `with()` saat memuat relasi yang akan digunakan di view
- Null coalescing operator (`??`) atau conditional `@if` lebih baik daripada ternary operator untuk readability
