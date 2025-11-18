# Tech Stack - SIAR (Sistem Informasi Administrasi Risiko)

## Ringkasan Eksekutif

SIAR adalah sistem informasi multi-tenant berbasis web yang dibangun menggunakan Laravel sebagai backend framework dan Bootstrap sebagai frontend framework. Aplikasi ini dirancang khusus untuk rumah sakit dan fasilitas kesehatan dengan arsitektur multi-tenant yang memungkinkan isolasi data antar tenant.

---

## Backend Framework & Core

### Laravel Framework
- **Versi**: Laravel 10.10
- **PHP Version**: PHP 8.1+
- **Deskripsi**: Framework PHP modern yang digunakan sebagai core aplikasi
- **Fitur yang Digunakan**:
  - MVC Architecture
  - Eloquent ORM
  - Blade Templating Engine
  - Routing System
  - Middleware System
  - Service Container & Service Providers
  - Artisan CLI
  - Database Migrations & Seeders
  - Queue System
  - Event & Listener System

### Laravel Sanctum
- **Versi**: ^3.3
- **Deskripsi**: Sistem autentikasi API token untuk aplikasi SPA dan mobile
- **Penggunaan**: API authentication untuk aplikasi mobile atau frontend terpisah

### Laravel Tinker
- **Versi**: ^2.8
- **Deskripsi**: REPL (Read-Eval-Print Loop) untuk interaksi dengan aplikasi Laravel via command line
- **Penggunaan**: Debugging dan testing interaktif

---

## Database & ORM

### Database Engine
- **Primary**: MySQL
- **Alternative**: SQLite (untuk development/testing)
- **Connection**: 
  - Default connection: `mysql`
  - Central connection: `mysql_central` (untuk session storage multi-tenant)
- **Charset**: utf8mb4
- **Collation**: utf8mb4_unicode_ci

### Doctrine DBAL
- **Versi**: ^3.9
- **Deskripsi**: Database Abstraction Layer untuk manipulasi schema database
- **Penggunaan**: Modifikasi kolom database, type casting, dan operasi schema lanjutan

### Eloquent ORM
- **Deskripsi**: ORM bawaan Laravel untuk interaksi database
- **Fitur yang Digunakan**:
  - Model Relationships (hasMany, belongsTo, belongsToMany, dll)
  - Global Scopes (untuk multi-tenant isolation)
  - Accessors & Mutators
  - Model Events
  - Soft Deletes
  - Query Builder

---

## Frontend Framework & Libraries

### Bootstrap
- **Versi**: ^5.3.2
- **Deskripsi**: CSS framework untuk membangun UI responsif
- **Penggunaan**: 
  - Grid system
  - Components (navbar, cards, modals, forms, dll)
  - Utilities classes
  - JavaScript components (tooltips, popovers, dropdowns)

### Vite
- **Versi**: ^5.0.0
- **Deskripsi**: Build tool modern untuk frontend assets
- **Plugin**: 
  - `laravel-vite-plugin`: ^1.0.0
  - `@vitejs/plugin-vue`: ^5.0.4 (tersedia, namun belum digunakan aktif)
- **Fitur**:
  - Hot Module Replacement (HMR)
  - Asset bundling & minification
  - Code splitting
  - Build optimization dengan hash-based filenames

### Axios
- **Versi**: ^1.6.4
- **Deskripsi**: HTTP client berbasis Promise untuk browser dan Node.js
- **Penggunaan**: AJAX requests, API calls dari frontend

### Chart.js
- **Versi**: 3.9.1 (via CDN)
- **Deskripsi**: Library JavaScript untuk membuat grafik dan chart interaktif
- **Penggunaan**: 
  - Dashboard statistics
  - Data visualization
  - Activity charts
  - Performance indicators

### Font Awesome
- **Versi**: ^6.4.2
- **Deskripsi**: Icon library berbasis font
- **Penggunaan**: Icons di seluruh aplikasi (via CDN dan npm package)

### Feather Icons
- **Versi**: ^4.29.2
- **Deskripsi**: Icon library minimalis berbasis SVG
- **Penggunaan**: Alternatif icon set untuk UI components

### Google Fonts (Bunny Fonts)
- **Font Family**: Figtree (400, 500, 600, 700)
- **Deskripsi**: Web font untuk typography
- **Penggunaan**: Font utama aplikasi

---

## Document & File Processing

### DomPDF (Laravel Package)
- **Package**: `barryvdh/laravel-dompdf`
- **Versi**: ^3.1
- **Deskripsi**: Laravel wrapper untuk DomPDF library
- **Penggunaan**: Generate PDF documents dari HTML/Blade templates

### PHPWord
- **Package**: `phpoffice/phpword`
- **Versi**: ^1.3
- **Deskripsi**: Library PHP untuk membuat dan memanipulasi dokumen Microsoft Word
- **Penggunaan**: Generate dokumen Word (.docx) untuk laporan dan template

### Intervention Image
- **Package**: `intervention/image`
- **Versi**: ^3.11
- **Deskripsi**: Library PHP untuk manipulasi dan processing gambar
- **Penggunaan**: 
  - Image resizing
  - Image optimization
  - Thumbnail generation
  - Image format conversion

---

## QR Code Generation

### Simple QR Code
- **Package**: `simplesoftwareio/simple-qrcode`
- **Versi**: ^4.2
- **Deskripsi**: Laravel wrapper untuk QR code generation
- **Penggunaan**: Generate QR codes untuk dokumen, identifikasi, dll

---

## Activity Logging & Monitoring

### Spatie Laravel Activity Log
- **Package**: `spatie/laravel-activitylog`
- **Versi**: ^4.10
- **Deskripsi**: Package untuk logging aktivitas user dan model changes
- **Penggunaan**: 
  - Audit trail
  - User activity tracking
  - Model change logging
  - Activity history

### Laravel Telescope
- **Versi**: ^5.6 (Development only)
- **Deskripsi**: Debug assistant untuk Laravel applications
- **Penggunaan**: 
  - Request monitoring
  - Query debugging
  - Exception tracking
  - Performance profiling
  - Log viewing
- **Note**: Hanya aktif di environment development

---

## Development Tools

### Laravel Sail
- **Versi**: ^1.18
- **Deskripsi**: Docker development environment untuk Laravel
- **Penggunaan**: Containerized development environment

### Laravel Pint
- **Versi**: ^1.0
- **Deskripsi**: Code style fixer untuk Laravel (berbasis PHP-CS-Fixer)
- **Penggunaan**: Code formatting dan style consistency

### PHPUnit
- **Versi**: ^10.1
- **Deskripsi**: Testing framework untuk PHP
- **Penggunaan**: Unit testing dan feature testing

### Faker
- **Package**: `fakerphp/faker`
- **Versi**: ^1.9.1
- **Deskripsi**: Library untuk generate fake data
- **Penggunaan**: Seeding database dengan data dummy untuk testing

### Mockery
- **Versi**: ^1.4.4
- **Deskripsi**: Mocking framework untuk PHPUnit
- **Penggunaan**: Mocking objects dalam unit tests

### Collision
- **Package**: `nunomaduro/collision`
- **Versi**: ^7.0
- **Deskripsi**: Error handler untuk command line applications
- **Penggunaan**: Better error display di terminal/console

### Spatie Laravel Ignition
- **Versi**: ^2.0
- **Deskripsi**: Error page untuk Laravel applications
- **Penggunaan**: Enhanced error pages dengan stack traces dan debugging info

---

## Server & Deployment

### Web Server
- **Development**: PHP Built-in Server
- **Production**: Apache/Nginx (tergantung hosting)
- **Port**: 8000 (development), 80/443 (production)

### PHP Version
- **Minimum**: PHP 8.1
- **Recommended**: PHP 8.2+

### Deployment Platform
- **Laravel Cloud**: Platform deployment khusus Laravel
- **Alternatives**: VPS, Shared Hosting, Cloud Platforms (AWS, DigitalOcean, dll)

---

## Caching & Session

### Cache Drivers
- **Default**: File-based caching
- **Alternatives**: 
  - Redis (tersedia, perlu konfigurasi)
  - Memcached (tersedia, perlu konfigurasi)
  - Database caching

### Session Driver
- **Default**: File-based sessions
- **Alternative**: Database sessions (untuk multi-tenant)
- **Lifetime**: 120 menit (konfigurasi)

### Queue Connection
- **Default**: Sync (synchronous)
- **Alternatives**: Database, Redis, SQS, dll

---

## Mail & Notifications

### Mail Driver
- **Default**: SMTP
- **Development**: Mailpit (port 1025)
- **Production**: SMTP server (konfigurasi sesuai kebutuhan)

---

## Storage & File System

### File System Disk
- **Default**: Local storage
- **Cloud Options**: 
  - AWS S3 (konfigurasi tersedia)
  - Other S3-compatible storage

### Storage Locations
- **Public**: `public/storage` (symlink ke `storage/app/public`)
- **Private**: `storage/app`
- **Backups**: `storage/app/backups`

---

## Security & Authentication

### Authentication
- **Method**: Laravel's built-in authentication
- **Session-based**: Cookie-based sessions
- **API**: Token-based (Laravel Sanctum)

### Authorization
- **Role-based Access Control (RBAC)**: Custom implementation
- **Permission System**: Module-based permissions
- **Middleware**: 
  - Authentication middleware
  - Permission middleware
  - Tenant middleware
  - Module access middleware

### Security Features
- **CSRF Protection**: Enabled
- **XSS Protection**: Blade templating auto-escaping
- **SQL Injection Protection**: Eloquent ORM parameter binding
- **Password Hashing**: Bcrypt/Argon2
- **Encryption**: Laravel's encryption service

---

## Multi-Tenant Architecture

### Tenant Resolution
- **Method**: Subdomain-based routing
- **Middleware**: 
  - `ResolveTenant`
  - `ResolveTenantByDomain`
  - `SetTenantId`
  - `EnsureTenantSession`
  - `TenantMiddleware`

### Data Isolation
- **Global Scopes**: Automatic tenant_id filtering
- **Database**: Shared database dengan tenant_id column
- **Session**: Central database connection untuk session storage

### Tenant Features
- **Module Activation**: Per-tenant module configuration
- **Custom Configuration**: Tenant-specific settings
- **Isolated Data**: Complete data separation per tenant

---

## Module System

### Modular Architecture
Aplikasi menggunakan struktur modular dengan modul-modul berikut:

1. **ActivityManagement**: Manajemen aktivitas dan tugas
2. **Correspondence**: Manajemen korespondensi
3. **Dashboard**: Dashboard utama aplikasi
4. **KendaliMutuBiaya**: Kontrol mutu dan biaya
5. **RiskManagement**: Manajemen risiko
6. **UserManagement**: Manajemen pengguna
7. **WorkUnit**: Manajemen unit kerja

### Module Structure
Setiap modul terdiri dari:
- Controller
- Model
- View (Blade templates)
- Routes
- Permissions
- Policies (jika diperlukan)

---

## API & Integration

### API Routes
- **File**: `routes/api.php`
- **Authentication**: Laravel Sanctum
- **Versioning**: Tersedia untuk future API versioning

### External Integrations
- **Future**: BPJS integration (planned)
- **Future**: Payment gateway integration (planned)

---

## Build & Development Tools

### Node.js Packages
- **Package Manager**: npm
- **Build Tool**: Vite
- **Module System**: ES Modules

### Build Process
```bash
# Development
npm run dev

# Production
npm run build
```

### Asset Management
- **CSS**: Compiled via Vite
- **JavaScript**: Bundled via Vite
- **Output**: `public/build/` directory
- **Manifest**: `public/build/manifest.json`

---

## Testing

### Testing Framework
- **PHPUnit**: ^10.1
- **Test Location**: `tests/` directory
- **Test Types**:
  - Unit Tests
  - Feature Tests

### Test Configuration
- **File**: `phpunit.xml`
- **Environment**: Testing environment dengan SQLite database

---

## Code Quality & Standards

### Code Style
- **Tool**: Laravel Pint
- **Standard**: PSR-12
- **Auto-fix**: Available via Pint

### Code Organization
- **PSR-4 Autoloading**: Implemented
- **Namespaces**: App namespace untuk application code
- **Helpers**: Custom helper functions di `app/Helpers/`

---

## Backup & Maintenance

### Database Backup
- **Method**: Automated daily backups via Laravel Scheduler
- **Location**: `storage/app/backups/`
- **Retention**: 10 latest backups
- **Format**: SQL dump files
- **Schedule**: Daily at 02:00 WIB

### Maintenance Scripts
- **Backup Script**: `scripts/backup_database.sh`
- **Scheduler Script**: `run_scheduler.sh`
- **Database Creation**: `scripts/create_db.sh`

---

## PWA (Progressive Web App)

### PWA Features
- **Manifest**: `public/manifest.json`
- **Service Worker**: `public/sw.js` (currently disabled)
- **Icons**: PWA icons di `public/images/pwa/`
- **Offline Support**: Planned (currently disabled)

---

## Environment Configuration

### Environment Files
- **Development**: `.env`
- **Production**: Environment variables di hosting platform

### Key Environment Variables
- `APP_NAME`: SIAR
- `APP_ENV`: production/development
- `APP_DEBUG`: true/false
- `DB_CONNECTION`: mysql/sqlite
- `SESSION_DRIVER`: file/database
- `CACHE_DRIVER`: file/redis/memcached
- `QUEUE_CONNECTION`: sync/database/redis

---

## Dependencies Summary

### PHP Dependencies (Production)
- `laravel/framework`: ^10.10
- `laravel/sanctum`: ^3.3
- `barryvdh/laravel-dompdf`: ^3.1
- `doctrine/dbal`: ^3.9
- `intervention/image`: ^3.11
- `phpoffice/phpword`: ^1.3
- `simplesoftwareio/simple-qrcode`: ^4.2
- `spatie/laravel-activitylog`: ^4.10

### PHP Dependencies (Development)
- `laravel/telescope`: ^5.6
- `laravel/sail`: ^1.18
- `laravel/pint`: ^1.0
- `phpunit/phpunit`: ^10.1
- `fakerphp/faker`: ^1.9.1
- `mockery/mockery`: ^1.4.4

### JavaScript Dependencies (Production)
- `bootstrap`: ^5.3.2
- `@fortawesome/fontawesome-free`: ^6.4.2
- `feather-icons`: ^4.29.2

### JavaScript Dependencies (Development)
- `vite`: ^5.0.0
- `laravel-vite-plugin`: ^1.0.0
- `@vitejs/plugin-vue`: ^5.0.4
- `axios`: ^1.6.4
- `glob`: ^11.0.2

### External CDN Resources
- Bootstrap JS: 5.3.2
- Font Awesome CSS: 6.4.2
- Chart.js: 3.9.1
- Google Fonts (Bunny Fonts): Figtree

---

## Version Control

### Git
- **Repository**: GitHub (terhubung dengan Laravel Cloud)
- **Branching Strategy**: TBD (disesuaikan dengan workflow tim)

---

## Documentation

### Documentation Files
- `README.md`: Overview aplikasi
- `docs/`: Dokumentasi teknis dan troubleshooting
- `DATABASE_GUIDELINES.md`: Panduan database
- `DEPLOYMENT-INSTRUCTIONS.md`: Instruksi deployment

---

## Future Considerations

### Planned Technologies
- **Vue.js**: Plugin tersedia, namun belum diimplementasikan aktif
- **Redis**: Untuk caching dan session (konfigurasi tersedia)
- **Queue Workers**: Untuk background jobs
- **WebSockets**: Untuk real-time features (Pusher/Laravel Echo)
- **API Versioning**: Untuk mobile app integration
- **GraphQL**: Alternative API approach (optional)

### Potential Improvements
- Implementasi Vue.js untuk SPA experience
- Redis untuk performance optimization
- Queue system untuk background processing
- WebSocket untuk real-time notifications
- API documentation (Swagger/OpenAPI)
- Automated testing coverage expansion

---

## Support & Maintenance

### Update Policy
- **Laravel**: Update sesuai dengan LTS schedule
- **Dependencies**: Regular security updates
- **PHP**: Update sesuai dengan PHP support lifecycle

### Monitoring
- **Laravel Telescope**: Development monitoring
- **Log Files**: `storage/logs/`
- **Error Tracking**: Laravel's exception handler

---

**Dokumen ini terakhir diperbarui**: 2024
**Versi Aplikasi**: SIAR v1.0
**Maintainer**: Development Team

