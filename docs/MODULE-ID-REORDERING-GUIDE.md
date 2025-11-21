# Panduan Mengubah ID Modul Secara Manual

## ⚠️ Peringatan Penting

Mengubah ID modul adalah operasi yang **sangat berisiko** karena:

- Semua tabel dengan Foreign Key ke `modules` akan terpengaruh
- Jika tidak dilakukan dengan benar, bisa menyebabkan data corruption
- Memerlukan downtime aplikasi

## Tabel yang Terpengaruh

Berikut tabel yang memiliki Foreign Key ke `modules.id`:

1. `tenant_modules` (module_id)
2. `role_module_permissions` (module_id)

## Solusi yang Tersedia

### Opsi 1: Artisan Command (RECOMMENDED) ✅

Command ini paling aman karena:

- ✅ Otomatis backup semua relasi
- ✅ Menggunakan database transaction
- ✅ Auto rollback jika ada error
- ✅ Progress bar untuk monitoring
- ✅ Dry-run mode untuk preview
- ✅ Mempertahankan semua relasi

#### Cara Penggunaan:

**1. Preview terlebih dahulu (Dry Run):**

```bash
php artisan modules:reorder-ids --dry-run
```

**2. Urutkan berdasarkan nama (default):**

```bash
php artisan modules:reorder-ids
```

**3. Urutkan berdasarkan slug:**

```bash
php artisan modules:reorder-ids --sort=slug
```

**4. Urutkan berdasarkan code:**

```bash
php artisan modules:reorder-ids --sort=code
```

#### Output Example:

```
🔄 Module ID Reordering Tool

Current Module IDs:
+------------+--------+------------------+----------------------+
| Current ID | New ID | Slug             | Name                 |
+------------+--------+------------------+----------------------+
| 3          | 1      | activity-mgmt    | Activity Management  |
| 7          | 2      | correspondence   | Correspondence       |
| 1          | 3      | dashboard        | Dashboard            |
| 5          | 4      | document-mgmt    | Document Management  |
+------------+--------+------------------+----------------------+

⚠️  This will reorder all module IDs. Continue? (yes/no) [no]:
> yes

Starting reordering process...

📦 Backing up relationships...
🔓 Disabling foreign key checks...
🗑️  Clearing relationships...
🔄 Reordering modules...
 4/4 [============================] 100%

📥 Restoring tenant_modules relationships...
 12/12 [============================] 100%

📥 Restoring role_module_permissions relationships...
 24/24 [============================] 100%

🔒 Re-enabling foreign key checks...

✅ Module IDs successfully reordered!

Final Module IDs:
+----+------------------+----------------------+
| ID | Slug             | Name                 |
+----+------------------+----------------------+
| 1  | activity-mgmt    | Activity Management  |
| 2  | correspondence   | Correspondence       |
| 3  | dashboard        | Dashboard            |
| 4  | document-mgmt    | Document Management  |
+----+------------------+----------------------+

📊 Statistics:
   Modules reordered: 4
   Tenant relationships restored: 12
   Role permissions restored: 24
```

---

### Opsi 2: Database Seeder

Seeder ini akan menghapus semua modul dan membuat ulang dengan ID berurutan.

#### Cara Penggunaan:

```bash
php artisan db:seed --class=ModuleResetSeeder
```

**⚠️ Peringatan:** Seeder ini akan menghapus dan membuat ulang semua modul!

---

### Opsi 3: Manual SQL (ADVANCED - TIDAK DISARANKAN) ⚠️

Jika Anda tetap ingin melakukan manual, berikut langkah-langkahnya:

#### Langkah 1: Backup Database

```bash
php artisan db:backup
# atau
mysqldump -u username -p database_name > backup.sql
```

#### Langkah 2: Disable Foreign Key Checks

```sql
SET FOREIGN_KEY_CHECKS=0;
```

#### Langkah 3: Backup Data Relasi

```sql
CREATE TEMPORARY TABLE temp_tenant_modules AS SELECT * FROM tenant_modules;
CREATE TEMPORARY TABLE temp_role_permissions AS SELECT * FROM role_module_permissions;
```

#### Langkah 4: Hapus Relasi

```sql
TRUNCATE TABLE role_module_permissions;
TRUNCATE TABLE tenant_modules;
```

#### Langkah 5: Update Module IDs

```sql
-- Contoh: Ubah ID modul dari 5 ke 1
UPDATE modules SET id = 1 WHERE id = 5;

-- Reset auto increment
ALTER TABLE modules AUTO_INCREMENT = 1;
```

#### Langkah 6: Restore Relasi dengan ID Baru

```sql
-- Restore tenant_modules
INSERT INTO tenant_modules (tenant_id, module_id, is_active, created_at, updated_at)
SELECT tenant_id,
       CASE
           WHEN module_id = 5 THEN 1  -- Mapping old ID to new ID
           WHEN module_id = 7 THEN 2
           -- dst...
       END as module_id,
       is_active, created_at, updated_at
FROM temp_tenant_modules;

-- Restore role_module_permissions
INSERT INTO role_module_permissions (role_id, module_id, can_view, can_create, can_edit, can_delete, can_export, can_import, created_at, updated_at)
SELECT role_id,
       CASE
           WHEN module_id = 5 THEN 1
           WHEN module_id = 7 THEN 2
           -- dst...
       END as module_id,
       can_view, can_create, can_edit, can_delete, can_export, can_import, created_at, updated_at
FROM temp_role_permissions;
```

#### Langkah 7: Enable Foreign Key Checks

```sql
SET FOREIGN_KEY_CHECKS=1;
```

#### Langkah 8: Verify

```sql
-- Cek jumlah data
SELECT COUNT(*) FROM modules;
SELECT COUNT(*) FROM tenant_modules;
SELECT COUNT(*) FROM role_module_permissions;

-- Cek integrity
SELECT tm.* FROM tenant_modules tm
LEFT JOIN modules m ON tm.module_id = m.id
WHERE m.id IS NULL;
```

---

## Best Practices

1. **Selalu backup database sebelum melakukan perubahan**
2. **Gunakan Artisan Command** (Opsi 1) - paling aman dan otomatis
3. **Test di development environment terlebih dahulu**
4. **Lakukan saat aplikasi sedang maintenance mode**
5. **Verifikasi data setelah proses selesai**

## Maintenance Mode

Sebelum melakukan reordering, aktifkan maintenance mode:

```bash
# Aktifkan maintenance mode
php artisan down --message="Sedang melakukan maintenance database"

# Jalankan reordering
php artisan modules:reorder-ids

# Nonaktifkan maintenance mode
php artisan up
```

## Troubleshooting

### Error: Foreign Key Constraint Fails

**Solusi:** Pastikan foreign key checks sudah disabled sebelum melakukan perubahan.

### Error: Duplicate Entry

**Solusi:** Gunakan temporary IDs terlebih dahulu, lalu ubah ke ID final.

### Data Hilang Setelah Reordering

**Solusi:** Restore dari backup dan gunakan Artisan Command yang sudah disediakan.

## Rekomendasi

**Gunakan Opsi 1 (Artisan Command)** karena:

- ✅ Paling aman
- ✅ Otomatis handle semua relasi
- ✅ Transaction-based (auto rollback on error)
- ✅ Progress monitoring
- ✅ Dry-run mode untuk preview
- ✅ Tidak perlu manual SQL

---

## File Terkait

- Command: `/app/Console/Commands/ReorderModuleIds.php`
- Seeder: `/database/seeders/ModuleResetSeeder.php`
- Documentation: `/docs/MODULE-ID-REORDERING-GUIDE.md`
