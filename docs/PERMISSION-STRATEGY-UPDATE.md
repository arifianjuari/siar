# Update Strategi Permission untuk Fitur dalam Modul

## Tanggal: 20 November 2025

## Perubahan Strategi

### Prinsip Baru

**Permission mengikuti modul utama, tidak perlu permission tambahan untuk fitur-fitur di dalam modul.**

Jika user sudah punya akses ke modul (melalui middleware `module:activity-management`), maka user otomatis punya akses ke semua fitur dalam modul tersebut.

### Pembagian Permission

#### 1. CRUD Utama Modul

**Tetap menggunakan permission spesifik:**

- `can_create` - Untuk create entity utama
- `can_edit` - Untuk edit entity utama
- `can_delete` - Untuk delete entity utama
- `can_view` - Untuk view entity utama (optional)

**Contoh di Activity Management:**

```php
// Create Activity - memerlukan can_create
Route::get('activities/create', [ActivityController::class, 'create'])
    ->middleware('check.permission:activity-management,can_create');

// Edit Activity - memerlukan can_edit
Route::get('activities/{uuid}/edit', [ActivityController::class, 'edit'])
    ->middleware('check.permission:activity-management,can_edit');

// Delete Activity - memerlukan can_delete
Route::delete('activities/{uuid}', [ActivityController::class, 'destroy'])
    ->middleware('check.permission:activity-management,can_delete');
```

#### 2. Fitur Tambahan dalam Modul

**Tidak menggunakan permission spesifik tambahan:**

- Comments
- Actionable Items
- Assignees
- Attachments
- Tags
- dll.

**Contoh di Activity Management:**

```php
// Actionable Items - TIDAK perlu permission tambahan
Route::post('activities/{uuid}/actionable-items', [ActionableItemController::class, 'store'])
    ->name('actionable-items.store');

// Comments - TIDAK perlu permission tambahan
Route::post('activities/{activityUuid}/comments', [ActivityCommentController::class, 'store'])
    ->name('comments.store');

// Assignees - TIDAK perlu permission tambahan
Route::post('activities/{uuid}/assignees', [ActivityAssigneeController::class, 'store'])
    ->name('assignees.store');
```

### Rasional

#### Keuntungan:

1. **Sederhana** - User tidak bingung dengan banyak permission
2. **Konsisten** - Satu modul = satu set permission
3. **Mudah maintain** - Tidak perlu setup permission untuk setiap fitur kecil
4. **UX lebih baik** - User yang sudah masuk modul bisa menggunakan semua fitur

#### Prinsip Keamanan:

- **Akses modul** sudah di-protect oleh middleware `module:activity-management`
- **CRUD utama** di-protect dengan permission spesifik
- **Fitur tambahan** mengikuti akses modul, karena fitur ini melengkapi entity utama
- **Tenant isolation** tetap terjaga melalui middleware `tenant`

## Implementasi

### Module: Activity Management

#### Routes yang Diubah:

```php
// ❌ SEBELUM - Permission check di setiap fitur
Route::post('activities/{uuid}/actionable-items', [ActionableItemController::class, 'store'])
    ->middleware('check.permission:activity-management,can_edit')
    ->name('actionable-items.store');

// ✅ SESUDAH - Hanya module access check
Route::post('activities/{uuid}/actionable-items', [ActionableItemController::class, 'store'])
    ->name('actionable-items.store');
```

#### View yang Diubah:

**Actionable Items Index (`modules/ActivityManagement/Resources/Views/actionable_items/index.blade.php`)**

```blade
<!-- ❌ SEBELUM -->
@if(auth()->user()->hasPermission('activity-management', 'can_edit'))
    <button type="button" class="btn btn-primary" data-bs-toggle="modal">
        <i class="fas fa-plus"></i> Tambah Item
    </button>
@endif

<!-- ✅ SESUDAH -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal">
    <i class="fas fa-plus"></i> Tambah Item
</button>
```

### File yang Dimodifikasi

1. **Routes:**

   - `routes/modules/ActivityManagement.php`
   - Menghapus middleware `check.permission` dari:
     - Actionable Items (store, update, destroy, toggle)
     - Activity Assignees (store, destroy)

2. **Views:**
   - `modules/ActivityManagement/Resources/Views/actionable_items/index.blade.php`
   - Menghapus `@if(auth()->user()->hasPermission())` checks dari:
     - Tombol "Tambah Item"
     - Checkbox toggle status
     - Tombol Edit & Delete

## Panduan untuk Modul Lain

### Checklist Permission Setup:

#### 1. Main Module Routes (routes/modules/{ModuleName}.php)

```php
Route::middleware(['web', 'auth', 'tenant', 'module:{module-name}'])
    ->prefix('{module-prefix}')
    ->name('{module-name}.')
    ->group(function () {

        // ✅ CRUD Utama - Gunakan permission check
        Route::get('create', [Controller::class, 'create'])
            ->middleware('check.permission:{module-name},can_create');
        Route::post('/', [Controller::class, 'store'])
            ->middleware('check.permission:{module-name},can_create');
        Route::get('{id}/edit', [Controller::class, 'edit'])
            ->middleware('check.permission:{module-name},can_edit');
        Route::patch('{id}', [Controller::class, 'update'])
            ->middleware('check.permission:{module-name},can_edit');
        Route::delete('{id}', [Controller::class, 'destroy'])
            ->middleware('check.permission:{module-name},can_delete');

        // ✅ Fitur Tambahan - TIDAK gunakan permission check
        Route::post('{id}/comments', [CommentController::class, 'store']);
        Route::delete('{id}/comments/{commentId}', [CommentController::class, 'destroy']);
        Route::post('{id}/attachments', [AttachmentController::class, 'store']);
        // dst...
    });
```

#### 2. Views - Fitur Tambahan

```blade
<!-- ✅ TIDAK perlu permission check untuk fitur tambahan -->
<button type="button" class="btn btn-primary">
    <i class="fas fa-plus"></i> Tambah
</button>

<!-- ❌ JANGAN seperti ini -->
@if(auth()->user()->hasPermission('{module}', 'can_edit'))
    <button>...</button>
@endif
```

#### 3. Views - CRUD Utama

```blade
<!-- ✅ Permission check untuk CRUD utama tetap perlu -->
@can('create', App\Models\EntityName::class)
    <a href="{{ route('{module}.create') }}" class="btn btn-primary">
        Tambah Baru
    </a>
@endcan

@can('update', $entity)
    <a href="{{ route('{module}.edit', $entity->id) }}" class="btn btn-info">
        Edit
    </a>
@endcan

@can('delete', $entity)
    <button class="btn btn-danger">Hapus</button>
@endcan
```

## Testing

### Test Cases:

1. **User dengan akses modul:**

   - ✅ Bisa view activities
   - ✅ Bisa tambah/edit/hapus actionable items
   - ✅ Bisa tambah/hapus assignees
   - ✅ Bisa tambah/hapus comments
   - ❌ Tidak bisa create activity (perlu can_create)
   - ❌ Tidak bisa edit activity (perlu can_edit)
   - ❌ Tidak bisa delete activity (perlu can_delete)

2. **User tanpa akses modul:**

   - ❌ Tidak bisa akses apapun di modul
   - Redirect ke halaman forbidden/dashboard

3. **User dengan can_create:**

   - ✅ Bisa create activity
   - ✅ Bisa akses semua fitur lainnya

4. **User dengan can_edit:**
   - ✅ Bisa edit activity
   - ✅ Bisa akses semua fitur lainnya

## Rollout ke Modul Lain

### Priority:

1. ✅ **Activity Management** - DONE
2. **Risk Management** - Review & update
3. **Document Management** - Review & update
4. **Correspondence** - Review & update
5. **Performance Management** - Review & update
6. **User Management** - Review & update

### Steps per Module:

1. Review routes file
2. Identify CRUD utama vs fitur tambahan
3. Hapus permission check dari fitur tambahan
4. Update views untuk fitur tambahan
5. Test dengan berbagai role
6. Update dokumentasi modul

## Impact Analysis

### Security Impact:

- **Minimal** - Module access tetap terkontrol
- **Tenant Isolation** - Tetap terjaga (middleware tenant)
- **RBAC** - Tetap berlaku untuk CRUD utama

### User Experience Impact:

- **Positive** - Lebih mudah digunakan
- **Consistent** - Behavior sama di semua modul
- **Less Confusion** - Tidak banyak "permission denied"

### Development Impact:

- **Simpler** - Tidak perlu setup permission untuk fitur kecil
- **Faster** - Development lebih cepat
- **Maintainable** - Lebih mudah di-maintain

## Catatan Penting

### ⚠️ Exception Cases

Ada beberapa case dimana fitur tambahan mungkin perlu permission tersendiri:

1. **Export/Import Data** - Karena sensitive operation
2. **Bulk Operations** - Karena high impact
3. **Settings/Configuration** - Karena affects semua user
4. **Approval Workflow** - Karena business logic khusus

Untuk case ini, boleh menggunakan permission spesifik dengan nama yang jelas:

```php
->middleware('check.permission:{module},can_export')
->middleware('check.permission:{module},can_import')
->middleware('check.permission:{module},can_configure')
->middleware('check.permission:{module},can_approve')
```

### 📋 Documentation Updates Needed

- Update Module Development Guide
- Update Permission Setup Guide
- Update Common Mistakes document
- Create permission strategy guide (this document)

## Related Documents

- [RBAC-MULTITENANT-IMPROVEMENTS.md](./RBAC-MULTITENANT-IMPROVEMENTS.md)
- [Module Development Guide](./Module%20Development%20Guide/)
- [ACTIONABLE-ITEMS-COMPLETE-FIX.md](./ACTIONABLE-ITEMS-COMPLETE-FIX.md)
