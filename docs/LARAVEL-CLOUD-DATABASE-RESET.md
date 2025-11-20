# Laravel Cloud Database Reset - Quick Guide

**Problem:** Laravel Cloud Console tidak support interactive input  
**Solution:** Gunakan force mode dengan safety flag

---

## 🚀 Command untuk Laravel Cloud

```bash
php artisan db:reset-production --force --i-understand-this-will-delete-all-data
```

---

## ⚠️ PENTING!

**Command ini akan:**

- ✅ Drop SEMUA tables
- ✅ Hapus SEMUA data
- ✅ Recreate database structure
- ✅ Create superadmin user
- ✅ Sync semua modules

**TIDAK BISA DI-UNDO!**

---

## 📋 Step-by-Step

### **1. Buka Laravel Cloud Console**

- Login ke Laravel Cloud
- Pilih project: `siar-beta`
- Click tab **"Console"**

### **2. Jalankan Command**

Copy-paste command ini:

```bash
php artisan db:reset-production --force --i-understand-this-will-delete-all-data
```

### **3. Tunggu Proses Selesai**

Output yang akan muncul:

```
⚠️  WARNING: This will reset the database!
This action will:
1. Drop all existing tables
2. Run all migrations
3. Create System tenant
4. Create superadmin role
5. Create superadmin@siar.com user
6. Sync all modules from filesystem

🚨 FORCE MODE: Skipping all confirmations!

Starting database reset...

Step 1/6: Running fresh migrations...
✓ Migrations completed

Step 2/6: Creating System tenant...
✓ System tenant created: ID=1

Step 3/6: Creating superadmin role...
✓ Superadmin role created: ID=1

Step 4/6: Creating superadmin user...
✓ Superadmin user created: superadmin@siar.com

Step 5/6: Syncing modules from filesystem...
  ✓ ActivityManagement
  ✓ Correspondence
  ✓ Dashboard
  ✓ DocumentManagement
  ✓ KendaliMutuBiaya
  ✓ PerformanceManagement
  ✓ ProductManagement
  ✓ RiskManagement
  ✓ SPOManagement
  ✓ UserManagement
  ✓ WorkUnit
✓ Synced 11 modules

Step 6/6: Summary
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
System Tenant ID: 1
Superadmin Role ID: 1
Superadmin User ID: 1
Email: superadmin@siar.com
Password: asdfasdf
Modules synced: 11
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✅ Database reset completed successfully!

IMPORTANT: Change the default password after first login!
```

### **4. Login & Test**

**URL:** https://siar-beta-ctegvo.laravel.cloud/login

**Credentials:**

- Email: `superadmin@siar.com`
- Password: `asdfasdf`

### **5. GANTI PASSWORD!** 🔒

**SEGERA ganti password setelah login!**

---

## 🛡️ Safety Features

### **Why Two Flags?**

```bash
# ❌ TIDAK AKAN JALAN (missing safety flag)
php artisan db:reset-production --force

# ✅ AKAN JALAN (dengan safety flag)
php artisan db:reset-production --force --i-understand-this-will-delete-all-data
```

**Alasan:**

- Prevent accidental execution
- Explicit acknowledgment required
- Extra layer of protection

### **Error Messages**

**Jika hanya gunakan `--force`:**

```
⚠️  FORCE MODE DETECTED!

For safety, you must also include:
--i-understand-this-will-delete-all-data

Full command:
php artisan db:reset-production --force --i-understand-this-will-delete-all-data
```

---

## 🔧 Troubleshooting

### **Error: "Command not found"**

**Cause:** Code belum ter-deploy

**Solution:**

1. Check deployment status di Laravel Cloud
2. Wait for auto-deploy to finish
3. Or trigger manual deploy

### **Error: "Database connection failed"**

**Cause:** Database credentials salah

**Solution:**

1. Check environment variables
2. Verify database is running
3. Check `.env` configuration

### **Error: "Modules directory not found"**

**Cause:** `modules/` directory tidak ada

**Solution:**

1. Verify deployment includes `modules/` folder
2. Check `.gitignore` tidak exclude modules
3. Ensure all modules committed to git

---

## 📊 What Gets Created

### **Database Structure**

| Table   | Records                  |
| ------- | ------------------------ |
| tenants | 1 (System)               |
| roles   | 1 (Super Admin)          |
| users   | 1 (superadmin@siar.com)  |
| modules | 11 (all from filesystem) |

### **Default User**

```
Name: Super Admin
Email: superadmin@siar.com
Password: asdfasdf
Role: superadmin
Tenant: System (ID=1)
```

### **Modules**

All modules from `modules/` directory:

1. ActivityManagement
2. Correspondence
3. Dashboard
4. DocumentManagement
5. KendaliMutuBiaya
6. PerformanceManagement
7. ProductManagement
8. RiskManagement
9. SPOManagement
10. UserManagement
11. WorkUnit

---

## 🔄 Post-Reset Tasks

### **Immediate (Required)**

- [ ] Login dengan superadmin@siar.com
- [ ] **Change password** dari `asdfasdf`
- [ ] Verify semua modules muncul di `/superadmin/modules`

### **Setup (As Needed)**

- [ ] Create tenants (hospitals/organizations)
- [ ] Create tenant admins
- [ ] Assign modules to tenants
- [ ] Create additional users
- [ ] Configure permissions

### **Maintenance**

- [ ] Clear caches:
  ```bash
  php artisan cache:clear
  php artisan config:clear
  php artisan view:clear
  php artisan route:clear
  ```

---

## 📝 Notes

- **Command:** `app/Console/Commands/ResetDatabaseProduction.php`
- **Documentation:** `docs/DATABASE-RESET-GUIDE.md`
- **Commit:** 6874193

---

## ⚡ Quick Copy-Paste

```bash
# Full command (copy this!)
php artisan db:reset-production --force --i-understand-this-will-delete-all-data
```

---

## 🆘 Need Help?

**Full documentation:** `docs/DATABASE-RESET-GUIDE.md`

**Common issues:**

1. Interactive mode tidak jalan → Use force mode
2. Force alone tidak jalan → Add safety flag
3. Modules tidak sync → Check modules/ directory exists

---

**Status:** ✅ Ready to use  
**Environment:** Laravel Cloud Console  
**Risk:** 🚨 HIGH - Complete data loss  
**Reversible:** ❌ NO

**Use with extreme caution!**
