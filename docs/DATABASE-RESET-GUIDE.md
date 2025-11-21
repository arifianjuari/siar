# Database Reset Guide - Production

**Date:** November 21, 2025  
**Purpose:** Reset database di server dengan setup ulang yang clean  
**Status:** Ready to use

---

## ⚠️ WARNING

**This is a DESTRUCTIVE operation!**

- ✅ Drops ALL existing tables
- ✅ Recreates database structure
- ✅ Removes ALL existing data
- ❌ CANNOT BE UNDONE

**Only use when:**

- Development/testing environment
- Fresh production setup needed
- Database corrupted beyond repair

---

## 🎯 What This Does

The `db:reset-production` command will:

1. **Drop all tables** - Complete database wipe
2. **Run migrations** - Recreate all tables
3. **Create System tenant** (ID=1, name='System')
4. **Create superadmin role** (slug='superadmin')
5. **Create superadmin user**:
   - Email: `superadmin@siar.com`
   - Password: `asdfasdf`
   - Role: superadmin
   - Tenant: System
6. **Sync all modules** from `modules/` directory

---

## 🚀 Usage

### **Method 1: Interactive (Local/SSH Only)**

```bash
# Via SSH or local terminal (NOT Laravel Cloud Console)
php artisan db:reset-production
```

**You will be prompted:**

1. Type `RESET DATABASE` to confirm (case-sensitive)
2. Confirm again with yes/no

**Safety checks:**

- Must type exact confirmation text
- Double confirmation required
- Shows what will happen before executing

⚠️ **NOTE:** Laravel Cloud Console does NOT support interactive input!

### **Method 2: Force Mode (For Laravel Cloud Console)**

```bash
# For non-interactive environments like Laravel Cloud Console
php artisan db:reset-production --force --i-understand-this-will-delete-all-data
```

**Safety features:**

- Requires TWO flags to execute
- `--force` alone will NOT work
- Must explicitly acknowledge data deletion

⚠️ **WARNING:** This skips all confirmations but requires explicit safety flag!

### **Method 3: Keep Existing Data**

```bash
# Only add missing data, don't drop tables
php artisan db:reset-production --keep-data
```

Use this to:

- Re-create superadmin user if deleted
- Re-sync modules without losing data
- Fix missing system records

---

## 📋 Step-by-Step Process

### **Step 1: Backup (if needed)**

```bash
# Backup existing database (optional)
php artisan backup:run --only-db
```

### **Step 2: Run Reset Command**

```bash
# Connect to Laravel Cloud console
# Navigate to Console tab

php artisan db:reset-production
```

### **Step 3: Confirmation**

```
⚠️  WARNING: This will reset the database!
This action will:
1. Drop all existing tables
2. Run all migrations
3. Create System tenant
4. Create superadmin role
5. Create superadmin@siar.com user
6. Sync all modules from filesystem

Type "RESET DATABASE" to confirm (case-sensitive):
```

Type exactly: `RESET DATABASE`

```
Are you absolutely sure? This cannot be undone! (yes/no) [no]:
```

Type: `yes`

### **Step 4: Wait for Completion**

```
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
  ...
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

---

## 🔑 Default Credentials

After reset, use these to login:

| Field        | Value                 |
| ------------ | --------------------- |
| **Email**    | `superadmin@siar.com` |
| **Password** | `asdfasdf`            |
| **Role**     | Super Admin           |
| **Tenant**   | System (ID=1)         |

**⚠️ CRITICAL:** Change password immediately after first login!

---

## 🗂️ Database Structure After Reset

### **Tenants Table**

```
ID | Name   | Slug   | Code   | Type   | Active
1  | System | system | SYSTEM | system | Yes
```

### **Roles Table**

```
ID | Name        | Slug       | Tenant ID
1  | Super Admin | superadmin | 1
```

### **Users Table**

```
ID | Name        | Email                | Role ID | Tenant ID
1  | Super Admin | superadmin@siar.com | 1       | 1
```

### **Modules Table**

All modules from `modules/` directory will be synced:

- ActivityManagement
- Correspondence
- Dashboard
- DocumentManagement
- KendaliMutuBiaya
- PerformanceManagement
- ProductManagement
- RiskManagement
- SPOManagement
- UserManagement
- WorkUnit

---

## 🔧 Troubleshooting

### **Error: "Confirmation text did not match"**

```bash
# You typed: reset database  (wrong case)
# You must type: RESET DATABASE  (exact case)
```

**Solution:** Type exactly `RESET DATABASE` (all caps)

### **Error: "This command can only be run in..."**

**Cause:** Environment check failed

**Solution:**

```bash
# Check environment
php artisan env

# Command only works in: production, staging, local
```

### **Error: "Modules directory not found"**

**Cause:** `modules/` directory missing or wrong path

**Solution:**

```bash
# Check if modules exist
ls -la modules/

# Ensure modules directory is deployed
git ls-files modules/
```

### **Error: Database connection failed**

**Cause:** Database credentials incorrect

**Solution:**

```bash
# Check .env file
cat .env | grep DB_

# Test connection
php artisan db:show
```

---

## 🛡️ Safety Features

### **1. Environment Check**

Only runs in: production, staging, local

### **2. Double Confirmation**

- Must type exact text
- Must confirm yes/no

### **3. Detailed Logging**

- Shows each step
- Shows errors with stack trace
- Transaction safety where possible

### **4. Graceful Error Handling**

- Catches exceptions
- Shows meaningful errors
- Returns proper exit codes

---

## 🔄 Alternative: Manual Reset

If command fails, manual steps:

```bash
# 1. Fresh migration
php artisan migrate:fresh --force

# 2. Create system tenant manually
php artisan tinker
>>> App\Models\Tenant::create(['id' => 1, 'name' => 'System', 'slug' => 'system', 'code' => 'SYSTEM', 'type' => 'system', 'is_active' => true]);

# 3. Create superadmin role
>>> $tenant = App\Models\Tenant::find(1);
>>> App\Models\Role::create(['name' => 'Super Admin', 'slug' => 'superadmin', 'description' => 'System administrator', 'tenant_id' => 1]);

# 4. Create superadmin user
>>> $role = App\Models\Role::where('slug', 'superadmin')->first();
>>> App\Models\User::create(['name' => 'Super Admin', 'email' => 'superadmin@siar.com', 'password' => bcrypt('asdfasdf'), 'email_verified_at' => now(), 'role_id' => $role->id, 'tenant_id' => 1, 'is_active' => true]);

# 5. Sync modules
>>> php artisan modules:sync --no-interaction
```

---

## 📊 Post-Reset Checklist

After successful reset:

- [ ] **Login test** with superadmin@siar.com
- [ ] **Change password** immediately
- [ ] **Verify modules** at /superadmin/modules
- [ ] **Create tenants** if needed
- [ ] **Create additional users** if needed
- [ ] **Test permissions** for each role
- [ ] **Clear caches**:
  ```bash
  php artisan cache:clear
  php artisan config:clear
  php artisan view:clear
  php artisan route:clear
  ```

---

## 🚨 Production Deployment

### **Before Running on Production:**

1. **Announce downtime** to all users
2. **Backup database** (if data needs to be preserved)
3. **Notify team** about reset
4. **Schedule maintenance window**

### **During Reset:**

1. Put site in **maintenance mode**:

   ```bash
   php artisan down --secret="your-secret-token"
   ```

2. Run reset command

3. Verify everything works

4. Bring site back up:
   ```bash
   php artisan up
   ```

### **After Reset:**

1. Test superadmin login
2. Create necessary tenants
3. Create tenant admins
4. Notify users system is ready
5. Monitor logs for issues

---

## 📝 Notes

- **Command location:** `app/Console/Commands/ResetDatabaseProduction.php`
- **Migrations used:** All files in `database/migrations/`
- **Modules source:** `modules/` directory
- **Default password:** `asdfasdf` (MUST be changed!)

---

## 🔗 Related

- **Module Sync:** `php artisan modules:sync --help`
- **Migrations:** `php artisan migrate --help`
- **Seeder:** `php artisan db:seed --help`

---

## ⚡ Quick Reference

```bash
# Interactive mode (SSH/Local only)
php artisan db:reset-production

# Force mode for Laravel Cloud Console (requires safety flag)
php artisan db:reset-production --force --i-understand-this-will-delete-all-data

# Keep data, only add missing
php artisan db:reset-production --keep-data

# Check command help
php artisan db:reset-production --help
```

---

**Status:** ✅ Ready for use  
**Safety:** 🛡️ Multiple confirmations required  
**Risk:** 🚨 HIGH - Data loss

**Use with extreme caution!**
