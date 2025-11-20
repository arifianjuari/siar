# Bug Fix: Role Tidak Ditampilkan di Halaman User Management

## Masalah

Role dari pengguna tidak ditampilkan di:

1. `/superadmin/users` - Halaman Manajemen Pengguna
2. `/superadmin/tenants/[id]` - Halaman Detail Tenant (bagian Manajemen Pengguna)

Padahal data role sudah ada di database dan user sudah memiliki `role_id` yang valid.

## Screenshot Masalah

- User dengan ID 6 memiliki `role_id = 20` di database
- User dengan ID 2 memiliki `role_id = 2` di database
- Tetapi di tampilan muncul "Tidak ada role"

## Root Cause Analysis

### **MASALAH UTAMA: Global Scope Tenant Filtering**

Model `Role` menggunakan trait `BelongsToTenant` yang menambahkan **global scope** untuk memfilter role berdasarkan `tenant_id` dari user yang sedang login.

**Skenario masalah:**

1. Superadmin login dengan `tenant_id = 1` (System)
2. Superadmin membuka halaman user management yang menampilkan user dari berbagai tenant
3. User dengan `tenant_id = 2` memiliki `role_id = 2` (role dengan `tenant_id = 2`)
4. Laravel mencoba eager load role, tetapi global scope memfilter hanya role dengan `tenant_id = 1`
5. Role tidak ditemukan karena `tenant_id` tidak cocok → `$user->role` menjadi `null`

**Kode di `BelongsToTenant` trait (app/Traits/BelongsToTenant.php line 18-26):**

```php
static::addGlobalScope('tenant_id', function (Builder $builder) {
    $tenantId = static::getCurrentTenantId();

    if ($tenantId) {
        $table = $builder->getModel()->getTable();
        $builder->where($table . '.tenant_id', $tenantId);
    }
});
```

### Mengapa Ini Terjadi?

Role model menggunakan `BelongsToTenant` trait:

```php
// app/Models/Role.php
class Role extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;
    // ...
}
```

Trait ini menambahkan global scope yang secara otomatis memfilter semua query Role berdasarkan tenant_id dari user yang sedang login. Ini bagus untuk isolasi tenant, tetapi menjadi masalah untuk superadmin yang perlu melihat data dari semua tenant.

## Solusi

### 1. UserManagementController (Halaman /superadmin/users)

**File**: `app/Http/Controllers/Superadmin/UserManagementController.php`

**Method `index()` - SEBELUM:**

```php
$query = User::with(['tenant', 'role']);
```

**Method `index()` - SESUDAH:**

```php
// Superadmin perlu melihat role dari semua tenant, jadi kita bypass tenant scope
$query = User::with([
    'tenant',
    'role' => function ($query) {
        $query->withoutGlobalScope('tenant_id');
    }
]);
```

**Method `show()` - SEBELUM:**

```php
$user->load(['tenant', 'role']);
```

**Method `show()` - SESUDAH:**

```php
$user->load([
    'tenant',
    'role' => function ($query) {
        $query->withoutGlobalScope('tenant_id');
    }
]);
```

### 2. TenantManagementController (Halaman /superadmin/tenants/[id])

**File**: `app/Http/Controllers/Superadmin/TenantManagementController.php`

**Method `show()` - SEBELUM:**

```php
$adminUsers = $tenant->users()->whereHas('role', function ($q) {
    $q->where('slug', 'tenant-admin');
})->get();
$users = $tenant->users()->paginate(10);
```

**Method `show()` - SESUDAH:**

```php
$adminUsers = $tenant->users()->whereHas('role', function ($q) {
    $q->withoutGlobalScope('tenant_id')->where('slug', 'tenant-admin');
})->get();

// Superadmin perlu melihat role dari semua tenant, jadi kita bypass tenant scope
$users = $tenant->users()->with(['role' => function ($query) {
    $query->withoutGlobalScope('tenant_id');
}])->paginate(10);
```

### 3. View Improvements

Kedua view sudah diupdate untuk menampilkan role dengan lebih baik:

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

## Penjelasan `withoutGlobalScope()`

Method `withoutGlobalScope('tenant_id')` digunakan untuk menonaktifkan global scope tenant filtering pada query tertentu. Ini aman digunakan di controller superadmin karena:

1. **Superadmin memiliki akses penuh** ke semua data dari semua tenant
2. **Sudah ada middleware** yang memastikan hanya superadmin yang bisa akses route ini
3. **Trait `BelongsToTenant` mencatat** setiap penggunaan `withoutGlobalScope()` ke log untuk audit

## Verifikasi

### Test Query di Tinker

```bash
php artisan tinker

# Simulasi superadmin login
$superadmin = App\Models\User::find(1);
Auth::login($superadmin);

# Test query dengan bypass global scope
$users = App\Models\User::with(['role' => function($q) {
    $q->withoutGlobalScope('tenant_id');
}])->get();

foreach($users as $user) {
    echo $user->name . ' | Role: ' . ($user->role ? $user->role->name : 'NULL') . PHP_EOL;
}
```

**Expected Output:**

```
Superadmin | Role: Superadmin
adminrsbbatu | Role: Tenant Admin
RS X | Role: Tenant Admin
```

### Clear Cache

```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

## Files Modified

1. **app/Http/Controllers/Superadmin/UserManagementController.php**

   - Line 24-29: Update query di method `index()` dengan `withoutGlobalScope`
   - Line 115-120: Update query di method `show()` dengan `withoutGlobalScope`

2. **app/Http/Controllers/Superadmin/TenantManagementController.php**

   - Line 25-30: Update query di method `index()` dengan `withoutGlobalScope` untuk roles count
   - Line 147-149: Update query `$adminUsers` dengan `withoutGlobalScope`
   - Line 151-153: Update query `$users` dengan `withoutGlobalScope`

3. **resources/views/roles/superadmin/users/index.blade.php**

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

4. **resources/views/roles/superadmin/tenants/show.blade.php**
   - Lines 349-355: Improved role display dengan conditional rendering

## Testing Checklist

- [x] Verifikasi data role ada di database
- [x] Verifikasi relasi User->Role berfungsi
- [x] Identifikasi masalah global scope tenant filtering
- [x] Update controller dengan `withoutGlobalScope()`
- [x] Update views untuk display role dengan benar
- [x] Test query dengan tinker
- [x] Clear all caches

## Instruksi untuk User

1. **Refresh halaman** dengan `Ctrl+R` (Windows/Linux) atau `Cmd+R` (Mac)
2. Atau **hard refresh** dengan `Ctrl+Shift+R` (Windows/Linux) atau `Cmd+Shift+R` (Mac)
3. Akses halaman:
   - http://siar.test/superadmin/users
   - http://siar.test/superadmin/tenants/[id]
4. Role seharusnya sudah ditampilkan dengan badge berwarna biru (bg-info)

## Debugging

Jika role masih tidak muncul:

1. Hover mouse ke badge "Tidak ada role" atau "-" untuk melihat tooltip yang menampilkan `role_id`
2. Check browser console untuk error JavaScript
3. Check Laravel log di `storage/logs/laravel.log`
4. Jalankan query test di tinker untuk memastikan data ada

## Catatan Penting

### Keamanan

- `withoutGlobalScope()` hanya digunakan di controller **Superadmin**
- Sudah ada middleware yang memastikan hanya superadmin yang bisa akses
- Setiap penggunaan `withoutGlobalScope()` dicatat di log untuk audit (lihat `BelongsToTenant` trait line 133-145)

### Best Practice

- Eager loading dengan `with()` mencegah N+1 query problem
- Selalu gunakan `withoutGlobalScope()` saat superadmin perlu akses data cross-tenant
- Untuk user biasa (non-superadmin), global scope tetap aktif untuk menjaga isolasi tenant

### Alternative Solution (Tidak Direkomendasikan)

Alternatif lain adalah menghapus `BelongsToTenant` trait dari model `Role`, tetapi ini akan:

- Menghilangkan isolasi tenant untuk role
- Memerlukan manual filtering di semua query
- Meningkatkan risiko data leak antar tenant

Solusi yang dipilih (menggunakan `withoutGlobalScope()`) lebih aman karena:

- Isolasi tenant tetap aktif secara default
- Hanya di-bypass di tempat yang memang diperlukan (superadmin area)
- Semua bypass tercatat di log untuk audit
