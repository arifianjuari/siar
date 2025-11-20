# Bug Fix: Tenant Creation Double Submission Issue

**Tanggal**: 19 November 2025  
**Severity**: Medium  
**Status**: Fixed

## Deskripsi Masalah

Terjadi keanehan saat pembuatan tenant di `/superadmin/tenants/create`:

1. User mengisi form dan klik tombol "Buat Tenant"
2. Muncul error: `SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'RS X' for key 'tenants.tenants_domain_unique'`
3. Namun setelah peringatan tersebut, tenant ternyata berhasil dibuat dan muncul di daftar

## Root Cause Analysis

### Penyebab Utama: Double Form Submission

Form tenant creation menggunakan AJAX submission dengan JavaScript, namun tidak memiliki proteksi terhadap double submission. Yang terjadi:

1. **User action**: Klik tombol submit (atau double-click tidak sengaja)
2. **Request pertama**: Dikirim ke server → **SUKSES** membuat tenant
3. **Request kedua**: Dikirim hampir bersamaan → **GAGAL** dengan duplicate error karena tenant sudah dibuat oleh request pertama
4. **Response handling**: JavaScript hanya menampilkan response dari request terakhir (yang gagal)
5. **User perspective**: Melihat error, padahal tenant sudah berhasil dibuat

### Kode Bermasalah

```javascript
// resources/views/roles/superadmin/tenants/create.blade.php (SEBELUM)
document.getElementById("tenant-form").addEventListener("submit", function (e) {
  e.preventDefault();
  const formData = new FormData(this);

  fetch(this.action, {
    method: "POST",
    body: formData,
    // ... headers
  });
  // ... no protection against double submission
});
```

**Masalah:**

- ❌ Tidak ada flag untuk mencegah multiple submissions
- ❌ Submit button tidak di-disable saat processing
- ❌ Tidak ada feedback visual bahwa request sedang diproses

## Solusi Implementasi

### 1. Tambah Double Submission Protection

**File**: `resources/views/roles/superadmin/tenants/create.blade.php`

**Perubahan:**

```javascript
let isSubmitting = false;

document.getElementById("tenant-form").addEventListener("submit", function (e) {
  e.preventDefault();

  // Prevent double submission
  if (isSubmitting) {
    return false;
  }

  isSubmitting = true;

  // Disable submit button dengan loading indicator
  const submitBtn = this.querySelector('button[type="submit"]');
  const originalBtnText = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML =
    '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';

  // ... fetch request

  // Re-enable button hanya jika ada error
  if (error) {
    isSubmitting = false;
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalBtnText;
  }
});
```

**Fitur yang ditambahkan:**

- ✅ `isSubmitting` flag mencegah multiple submissions
- ✅ Button disabled dengan loading spinner sebagai feedback visual
- ✅ Button re-enabled hanya jika ada error (untuk retry)
- ✅ Jika sukses, redirect terjadi tanpa re-enable button (prevent accidental resubmit)

## Testing Guide

### Test Case 1: Normal Submission

1. Akses `/superadmin/tenants/create`
2. Isi form dengan data valid
3. Klik "Buat Tenant" sekali
4. **Expected**:
   - Button berubah menjadi "Memproses..." dan disabled
   - Tenant berhasil dibuat
   - Redirect ke index page
   - Tidak ada error message

### Test Case 2: Double Click Prevention

1. Akses `/superadmin/tenants/create`
2. Isi form dengan data valid
3. Klik "Buat Tenant" **dua kali cepat** (double-click)
4. **Expected**:
   - Hanya 1 request terkirim ke server
   - Tenant hanya dibuat 1x
   - Tidak ada duplicate error

### Test Case 3: Error Handling

1. Akses `/superadmin/tenants/create`
2. Isi form dengan domain yang sudah ada
3. Klik "Buat Tenant"
4. **Expected**:
   - Error message muncul
   - Button kembali enabled untuk retry
   - User bisa memperbaiki dan submit ulang

## Additional Recommendations

### 1. Server-Side Rate Limiting

Tambahkan rate limiting untuk prevent abuse:

```php
// app/Http/Kernel.php
'api' => [
    'throttle:60,1', // 60 requests per minute
]
```

### 2. Idempotency Key (Optional)

Untuk proteksi lebih kuat, tambahkan idempotency key:

```javascript
// Generate unique key per form load
const idempotencyKey = Date.now() + "-" + Math.random().toString(36);

fetch(this.action, {
  headers: {
    "X-Idempotency-Key": idempotencyKey,
    // ... other headers
  },
});
```

```php
// Controller
public function store(Request $request) {
    $key = $request->header('X-Idempotency-Key');
    if ($key && Cache::has('idempotency:' . $key)) {
        return Cache::get('idempotency:' . $key);
    }

    // ... create tenant

    if ($key) {
        Cache::put('idempotency:' . $key, $response, 300); // 5 minutes
    }

    return $response;
}
```

### 3. Apply Same Fix to Other Forms

Forms yang mungkin memerlukan perbaikan serupa:

- `/superadmin/tenants/{id}/edit` - Update tenant
- `/superadmin/modules/create` - Create module
- Tenant user creation forms
- Role creation forms

## Impact

- **Users Affected**: Superadmin yang membuat tenant baru
- **Frequency**: Terjadi saat double-click atau koneksi lambat
- **Severity**: Medium (confusing UX, data integrity tetap terjaga)
- **Fix Complexity**: Low
- **Test Coverage**: Manual testing required

## Verification

Setelah deployment, verifikasi dengan:

```bash
# Check tenant creation logs
tail -f storage/logs/laravel.log | grep "tenant"

# Check for duplicate tenants
mysql -u root -p siar -e "SELECT domain, COUNT(*) as count FROM tenants GROUP BY domain HAVING count > 1;"
```

## Rollback Plan

Jika terjadi masalah, rollback dengan:

```bash
git revert <commit-hash>
```

Atau manual restore file:

```bash
git checkout HEAD~1 -- resources/views/roles/superadmin/tenants/create.blade.php
```

## Related Issues

- None (new issue)

## References

- Laravel Validation: https://laravel.com/docs/validation
- JavaScript Form Handling: https://developer.mozilla.org/en-US/docs/Web/API/FormData
- Idempotency Pattern: https://stripe.com/docs/api/idempotent_requests
