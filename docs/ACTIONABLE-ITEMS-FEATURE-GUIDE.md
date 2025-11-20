# Panduan Fitur Kelola Item Aksi

## Overview

Fitur **Kelola Item Aksi** sudah tersedia lengkap dengan CRUD functionality. Fitur ini memungkinkan pengguna untuk mengelola daftar item tindakan yang terkait dengan setiap aktivitas.

## Status Implementasi

✅ **SUDAH TERSEDIA LENGKAP**

### Komponen yang Sudah Ada:

#### 1. **Database Schema** ✅

- Tabel: `actionable_items`
- Kolom lengkap termasuk: uuid, title, description, due_date, priority, order, status, dll.
- Migration sudah dijalankan

#### 2. **Model** ✅

**File:** `modules/ActivityManagement/Models/ActionableItem.php`

Fitur:

- UUID auto-generation
- Relationships: activity, actionable (polymorphic), creator, updater, completer
- Accessor attributes:
  - `priority_label` - Label prioritas (Rendah, Sedang, Tinggi, Kritis)
  - `priority_color` - Warna badge prioritas
  - `status_label` - Label status (Menunggu, Dalam Proses, Selesai, Dibatalkan)
  - `status_color` - Warna badge status
  - `time_remaining` - Waktu relatif deadline

#### 3. **Controller** ✅

**File:** `modules/ActivityManagement/Http/Controllers/ActionableItemController.php`

Methods:

- `index($uuid)` - Tampilkan daftar item aksi
- `store(Request $request, $uuid)` - Tambah item baru
- `update(Request $request, $uuid, $itemUuid)` - Update item
- `destroy($uuid, $itemUuid)` - Hapus item
- `toggle($uuid, $itemUuid)` - Toggle status completed

#### 4. **Routes** ✅

**File:** `routes/modules/ActivityManagement.php`

```php
// Actionable Items
Route::get('activities/{uuid}/actionable-items', [ActionableItemController::class, 'index'])
    ->name('actionable-items.index');
Route::post('activities/{uuid}/actionable-items', [ActionableItemController::class, 'store'])
    ->middleware('check.permission:activity-management,can_edit')
    ->name('actionable-items.store');
Route::put('activities/{uuid}/actionable-items/{itemUuid}', [ActionableItemController::class, 'update'])
    ->middleware('check.permission:activity-management,can_edit')
    ->name('actionable-items.update');
Route::delete('activities/{uuid}/actionable-items/{itemUuid}', [ActionableItemController::class, 'destroy'])
    ->middleware('check.permission:activity-management,can_edit')
    ->name('actionable-items.destroy');
Route::put('activities/{uuid}/actionable-items/{itemUuid}/toggle', [ActionableItemController::class, 'toggle'])
    ->middleware('check.permission:activity-management,can_edit')
    ->name('actionable-items.toggle');
```

#### 5. **Views** ✅

**File:** `modules/ActivityManagement/Resources/Views/actionable_items/index.blade.php`

Fitur UI:

- Tabel daftar item aksi dengan kolom:
  - Checkbox untuk toggle status
  - Judul dan deskripsi
  - Badge prioritas (dengan warna)
  - Badge status (dengan warna)
  - Tenggat waktu
  - Info pembuat
  - Tombol aksi (Edit & Hapus)
- Modal untuk Tambah Item
- Modal untuk Edit Item
- AJAX handlers untuk semua operasi CRUD
- Konfirmasi hapus
- Auto-reload setelah operasi

#### 6. **Integration dengan Activity Detail** ✅

**File:** `modules/ActivityManagement/Resources/Views/show.blade.php`

- Card "Item Tindakan" di halaman detail aktivitas
- Tombol "Kelola" yang mengarah ke halaman Kelola Item Aksi
- Preview item aksi di halaman detail

## Cara Menggunakan

### 1. Akses Halaman Kelola Item Aksi

Dari halaman detail aktivitas:

1. Buka detail aktivitas tertentu
2. Scroll ke section "Item Tindakan"
3. Klik tombol **"Kelola"**

URL Pattern: `/activity-management/activities/{uuid}/actionable-items`

### 2. Tambah Item Aksi Baru

1. Klik tombol **"Tambah Item"**
2. Isi form:
   - **Judul** (required)
   - **Deskripsi** (optional)
   - **Tenggat Waktu** (optional)
   - **Prioritas** (required): Rendah, Sedang, Tinggi, Kritis
3. Klik **"Simpan"**

### 3. Edit Item Aksi

1. Klik tombol **Edit** (ikon pensil) pada item yang ingin diedit
2. Ubah data di form modal
3. Klik **"Simpan"**

### 4. Hapus Item Aksi

1. Klik tombol **Hapus** (ikon tempat sampah)
2. Konfirmasi penghapusan
3. Item akan dihapus

### 5. Toggle Status Completed

- Centang checkbox di sebelah item untuk menandai sebagai selesai
- Uncheck untuk mengembalikan ke status pending

## Permissions

Fitur ini menggunakan permission module `activity-management`:

- **View**: Semua user dengan akses module bisa melihat
- **Create/Edit/Delete**: Memerlukan permission `can_edit`

## Data Structure

### Prioritas

- `low` - Rendah (badge secondary)
- `medium` - Sedang (badge info)
- `high` - Tinggi (badge warning)
- `critical` - Kritis (badge danger)

### Status

- `pending` - Menunggu (badge warning)
- `in_progress` - Dalam Proses (badge info)
- `completed` - Selesai (badge success)
- `cancelled` - Dibatalkan (badge secondary)

## Technical Details

### Validation Rules

**Create:**

```php
'title' => 'required|string|max:255',
'description' => 'nullable|string',
'due_date' => 'nullable|date',
'priority' => 'required|in:low,medium,high,critical',
```

**Update:**

```php
'title' => 'required|string|max:255',
'description' => 'nullable|string',
'due_date' => 'nullable|date',
'priority' => 'required|in:low,medium,high,critical',
'status' => 'required|in:pending,in_progress,completed,cancelled',
```

### Auto-Generated Fields

- `uuid` - Auto-generated on create
- `order` - Auto-calculated (max + 1)
- `created_by` - Current user ID
- `updated_by` - Current user ID on update
- `completed_at` - Timestamp when marked as completed
- `completed_by` - User ID who completed the item

## Testing Checklist

- [x] Database schema created
- [x] Model with relationships
- [x] Controller with CRUD methods
- [x] Routes registered
- [x] Views with modals
- [x] AJAX handlers
- [x] Permission checks
- [x] Integration with activity detail page
- [x] Accessor attributes for labels and colors

## Next Steps (Optional Enhancements)

1. ✅ Add drag-and-drop reordering
2. ✅ Add bulk operations
3. ✅ Add filtering by status/priority
4. ✅ Add export functionality
5. ✅ Add email notifications for due dates
6. ✅ Add activity log for item changes

## Troubleshooting

### Error: Column not found 'order'

**Status:** ✅ FIXED

- Migration sudah dibuat dan dijalankan
- Semua kolom yang diperlukan sudah ada

### Error: Undefined property priority_label

**Status:** ✅ FIXED

- Accessor methods sudah ditambahkan ke model

### Items tidak muncul

- Pastikan activity memiliki items
- Check permission `can_edit` untuk menambah items
- Verify route middleware

## Related Documentation

- [ACTIONABLE-ITEMS-SCHEMA-FIX.md](./ACTIONABLE-ITEMS-SCHEMA-FIX.md) - Detail fix untuk schema issue
- [Module Development Guide](./Module%20Development%20Guide/) - Panduan umum development module
