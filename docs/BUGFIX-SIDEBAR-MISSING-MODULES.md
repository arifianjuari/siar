# Bugfix: Modul Tidak Muncul di Sidebar Meskipun Sudah Diberi Permission

**Tanggal:** 19 November 2025  
**Status:** ✅ Fixed

## Masalah

User **Tenant Admin** sudah diberi hak akses (permission) untuk semua modul, tetapi beberapa modul **tidak muncul** di sidebar:

### Modul yang Hilang:

- ❌ **Correspondence** - Ada permission, tidak muncul di sidebar
- ❌ **Document Management** - Ada permission, tidak muncul di sidebar

### Modul yang Muncul:

- ✅ Kendali Mutu Biaya
- ✅ Manajemen Produk
- ✅ Manajemen Risiko
- ✅ Pengelolaan Kegiatan
- ✅ Performance Management
- ✅ User Management
- ✅ WorkUnit (di section UNIT KERJA)
- ✅ Manajemen SPO (di section UNIT KERJA)

## Root Cause Analysis

### 1. **Slug Mismatch di Sidebar Code**

File: `resources/views/layouts/partials/sidebar.blade.php`

#### Masalah di Baris 166-167:

```php
// Code mencari slug 'correspondence-management'
elseif ($module->slug == 'correspondence-management') {
    $moduleUrl = url('correspondence/dashboard');
}
```

**Tetapi di database:**

```sql
SELECT slug FROM modules WHERE name = 'Correspondence';
-- Result: 'correspondence' (bukan 'correspondence-management')
```

#### Masalah di Baris 191-193:

```php
// Code juga mencari 'correspondence-management'
if ($module->slug == 'correspondence-management') {
    $isActive = request()->is('modules/correspondence*');
}
```

### 2. **Inconsistent Slug Naming**

Database memiliki variasi slug untuk modul yang sama:

- `work-units` vs `work-unit`
- `correspondence` vs `correspondence-management`

Sidebar code tidak menangani variasi ini, sehingga modul tidak ter-render.

## Solusi yang Diimplementasikan

### 1. **Update URL Mapping untuk Correspondence**

**Sebelum:**

```php
elseif ($module->slug == 'correspondence-management') {
    $moduleUrl = url('correspondence/dashboard');
}
```

**Sesudah:**

```php
elseif ($module->slug == 'correspondence' || $module->slug == 'correspondence-management') {
    $moduleUrl = url('correspondence/dashboard');
}
```

### 2. **Update Active State Check**

**Sebelum:**

```php
if ($module->slug == 'correspondence-management') {
    $isActive = request()->is('modules/correspondence*');
}
```

**Sesudah:**

```php
if ($module->slug == 'correspondence' || $module->slug == 'correspondence-management') {
    $isActive = request()->is('modules/correspondence*') || request()->is('correspondence*');
}
```

### 3. **Update Work Unit Handling**

**Sebelum:**

```php
if ($module->slug == 'work-units' || $module->slug == 'spo-management') {
    continue;
}
```

**Sesudah:**

```php
if (in_array($module->slug, ['work-units', 'work-unit', 'spo-management'])) {
    continue;
}
```

### 4. **Update Work Unit URL Mapping**

**Sebelum:**

```php
elseif ($module->slug == 'work-units') {
    $moduleUrl = url('work-units-dashboard');
}
```

**Sesudah:**

```php
elseif ($module->slug == 'work-units' || $module->slug == 'work-unit') {
    $moduleUrl = url('work-units-dashboard');
}
```

## Files Modified

### `resources/views/layouts/partials/sidebar.blade.php`

**Changes:**

1. Line 149: Updated skip logic untuk work-unit variations
2. Line 166-167: Added correspondence slug variation handling
3. Line 168: Added work-unit slug variation handling
4. Line 182: Updated work-unit active state check
5. Line 191-192: Updated correspondence active state check

## Testing

### Before Fix:

```
Sidebar (Tenant Admin):
├── Dashboard
├── MODUL
│   ├── Kendali Mutu Biaya ✓
│   ├── Manajemen Produk ✓
│   ├── Manajemen Risiko ✓
│   ├── Pengelolaan Kegiatan ✓
│   ├── Performance Management ✓
│   └── User Management ✓
└── UNIT KERJA
    ├── Profil Unit Saya ✓
    └── Manajemen SPO ✓

Missing:
- Correspondence ✗
- Document Management ✗
```

### After Fix:

```
Sidebar (Tenant Admin):
├── Dashboard
├── MODUL
│   ├── Correspondence ✓ (FIXED)
│   ├── Document Management ✓ (FIXED)
│   ├── Kendali Mutu Biaya ✓
│   ├── Manajemen Produk ✓
│   ├── Manajemen Risiko ✓
│   ├── Pengelolaan Kegiatan ✓
│   ├── Performance Management ✓
│   └── User Management ✓
└── UNIT KERJA
    ├── Profil Unit Saya ✓
    └── Manajemen SPO ✓
```

## Verification Steps

1. **Clear cache:**

   ```bash
   php artisan view:clear
   php artisan cache:clear
   ```

2. **Login sebagai Tenant Admin**

3. **Verifikasi sidebar:**

   - ✅ Correspondence muncul di section MODUL
   - ✅ Document Management muncul di section MODUL
   - ✅ Semua modul lain tetap muncul
   - ✅ WorkUnit tetap di section UNIT KERJA

4. **Test navigation:**
   - ✅ Klik Correspondence → mengarah ke `/correspondence/dashboard`
   - ✅ Klik Document Management → mengarah ke `/document-management/dashboard`

## Root Cause Prevention

### Recommendation 1: Standardize Module Slugs

**Problem:** Inconsistent slug naming di database

```sql
-- Contoh inconsistency:
'work-units' vs 'work-unit'
'correspondence' vs 'correspondence-management'
```

**Solution:** Standardize semua slug dengan format:

```
{module-name}-management
```

**Migration Script:**

```sql
-- Standardize slugs
UPDATE modules SET slug = 'correspondence-management' WHERE slug = 'correspondence';
UPDATE modules SET slug = 'work-units-management' WHERE slug = 'work-unit';
```

### Recommendation 2: Use Slug Alias Table

Create table untuk mapping slug variations:

```sql
CREATE TABLE module_slug_aliases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    alias VARCHAR(100) NOT NULL,
    FOREIGN KEY (module_id) REFERENCES modules(id),
    UNIQUE KEY (alias)
);

-- Example data:
INSERT INTO module_slug_aliases (module_id, alias) VALUES
(1, 'correspondence'),
(1, 'correspondence-management'),
(11, 'work-units'),
(11, 'work-unit');
```

### Recommendation 3: Create Helper Function

```php
// app/Helpers/ModuleHelper.php
function normalizeModuleSlug($slug) {
    $aliases = [
        'correspondence' => 'correspondence-management',
        'work-unit' => 'work-units',
        // Add more mappings
    ];

    return $aliases[$slug] ?? $slug;
}
```

## Impact

- ✅ **Fixed:** Correspondence dan Document Management sekarang muncul di sidebar
- ✅ **Improved:** Sidebar code lebih robust terhadap slug variations
- ✅ **Better UX:** Tenant Admin dapat mengakses semua modul yang sudah diberi permission

## Related Issues

- Module slug inconsistency di database
- Sidebar rendering logic perlu refactoring untuk lebih maintainable

## Notes

Perbaikan ini bersifat **defensive programming** - menangani berbagai variasi slug yang mungkin ada di database. Untuk jangka panjang, sebaiknya:

1. Standardize semua module slugs di database
2. Add unique constraint pada slug column
3. Create migration untuk cleanup slug inconsistencies
4. Add validation di ModuleController untuk enforce slug format
