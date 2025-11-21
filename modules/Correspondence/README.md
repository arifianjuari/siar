# Correspondence Module

## Overview

The Correspondence module provides comprehensive functionality for managing correspondence letters (surat menyurat), including incoming and outgoing letters, official memos (nota dinas), and reports. It supports document management, tagging, exporting, and QR code generation.

## Module Structure

```
modules/Correspondence/
├── Config/
│   └── config.php              # Module configuration
├── Database/
│   ├── Migrations/             # Database migrations
│   └── Seeders/                # Database seeders
├── Http/
│   ├── Controllers/            # Module controllers
│   │   ├── CorrespondenceController.php
│   │   └── ReportController.php
│   ├── Middleware/             # Module-specific middleware
│   ├── Requests/               # Form request validation
│   └── routes.php              # Module routes
├── Models/                     # Module models
│   └── Correspondence.php
├── Providers/                  # Service providers
│   └── CorrespondenceServiceProvider.php
├── Resources/
│   ├── Views/                  # Module views
│   │   ├── dashboard.blade.php
│   │   ├── letters/
│   │   └── reports/
│   └── Assets/                 # Module assets (CSS, JS, images)
├── Services/                   # Business logic services
├── Tests/                      # Module tests
├── module.json                 # Module metadata
└── README.md                   # This file
```

## Features

### Letter Management

- **CRUD Operations**: Create, read, update, delete correspondence letters
- **Document Types**: Support for various letter types (internal, external, memos)
- **Document Metadata**: Title, number, date, sender, recipient, subject, body
- **Confidentiality Levels**: Public, Internal, Confidential, Restricted
- **File Attachments**: Upload and manage letter files
- **Document Links**: External document references

### Signatory Management

- **Signatory Information**: Name, position, rank, NRP
- **Signature Files**: Upload digital signatures
- **Signing Details**: Location and date of signing

### Tagging System

- **Tag Assignment**: Attach multiple tags to letters
- **Tag-based Filtering**: Search and filter by tags
- **Polymorphic Relationship**: Shared tag system across modules

### Export & Generation

- **PDF Export**: Generate formatted PDF documents
- **Word Export**: Export to Microsoft Word format
- **QR Code Generation**: Generate QR codes for letter verification
- **Base64 QR Codes**: Embedded QR codes in documents

### Reporting

- **Statistics Dashboard**: Overview of correspondence metrics
- **Monthly Charts**: Visual representation of letter trends
- **Status Charts**: Letter status distribution
- **Custom Reports**: Generate reports by date range, type, status
- **Report Export**: Export reports to PDF

## Models

### Correspondence Model

Located in `Modules\Correspondence\Models\Correspondence`

**Attributes:**

- `tenant_id` - Foreign key to tenant
- `work_unit_id` - Foreign key to work unit
- `document_number` - Official document number
- `document_title` - Letter title
- `document_type` - Type of document
- `document_version` - Version number
- `document_date` - Date of document
- `confidentiality_level` - Confidentiality classification
- `file_path` - Path to uploaded file
- `document_link` - External document link
- `next_review` - Next review date
- `origin_module` - Source module (if generated from other module)
- `origin_record_id` - Source record ID
- `subject` - Letter subject
- `body` - Letter content/body
- `reference_to` - Reference to other documents
- `sender_name` - Sender's name
- `sender_position` - Sender's position
- `recipient_name` - Recipient's name
- `recipient_position` - Recipient's position
- `cc_list` - Carbon copy list
- `signed_at_location` - Signing location
- `signed_at_date` - Signing date
- `signatory_name` - Signatory's name
- `signatory_position` - Signatory's position
- `signatory_rank` - Signatory's rank
- `signatory_nrp` - Signatory's NRP (employee number)
- `signature_file` - Signature file path
- `created_by` - Creator user ID

**Relationships:**

- `creator()` - Belongs to User (created_by)
- `tags()` - Morph to many Tag
- `documents()` - Morph to many Document
- `workUnit()` - Belongs to WorkUnit

**Backward Compatibility:**

- A backward compatibility adapter exists at `App\Models\Correspondence`

## Routes

All routes are prefixed with `/correspondence/` and protected by authentication and module access middleware.

### Main Routes:

- `GET /correspondence/dashboard` - Dashboard with statistics
- `GET /correspondence/letters` - List all letters
- `GET /correspondence/letters/create` - Create new letter form
- `POST /correspondence/letters` - Store new letter
- `GET /correspondence/letters/{id}` - View letter details
- `GET /correspondence/letters/{id}/edit` - Edit letter form
- `PUT /correspondence/letters/{id}` - Update letter
- `DELETE /correspondence/letters/{id}` - Delete letter

### Export Routes:

- `GET /correspondence/letters/{id}/export-pdf` - Export letter to PDF
- `GET /correspondence/letters/{id}/export-word` - Export letter to Word

### QR Code Routes:

- `GET /correspondence/letters/{id}/qr-code` - Generate QR code
- `GET /correspondence/letters/{id}/qr-code-base64` - Generate base64 QR code
- `GET /correspondence/qr-test` - QR code test page

### Search & Reports:

- `GET /correspondence/search` - Search and filter letters
- `GET /correspondence/reports` - Reports index
- `GET /correspondence/reports/generate` - Generate report
- `POST /correspondence/reports/export` - Export report

## Permissions

The module uses middleware check:

- `module:correspondence-management` - Access to correspondence module

## Usage in Views

Views can be referenced using the module namespace:

```blade
@extends('correspondence::layouts.master')
@include('correspondence::partials.header')
```

## File Storage

Documents and signatures are stored using Laravel's Storage facade:

- Letters: `storage/app/public/correspondence/`
- Signatures: `storage/app/public/signatures/`

## Dependencies

- Laravel Framework 10.x
- PHP 8.1+
- Requires authentication middleware
- Requires tenant middleware (multi-tenancy)
- Requires module:correspondence-management middleware
- Barryvdh/laravel-dompdf for PDF generation
- SimpleSoftwareIO/simple-qrcode for QR codes
- PhpOffice/PHPWord for Word export

## Installation

1. Module is auto-discovered via CorrespondenceServiceProvider
2. Run migrations if any: `php artisan migrate`
3. Ensure storage is linked: `php artisan storage:link`
4. Clear cache: `php artisan cache:clear`
5. Module will be available at `/correspondence/`

## Key Features Detail

### Document Management

- **Version Control**: Track document versions
- **Review Cycle**: Set next review dates
- **Origin Tracking**: Track if letter originated from other modules
- **File Upload**: Support for various file formats
- **External Links**: Reference external documents

### Dashboard Analytics

- Total correspondence count
- Monthly correspondence trends
- Status distribution charts
- Recent letters listing
- Quick statistics by type

### QR Code Integration

- **SVG QR Codes**: Scalable vector graphics
- **Base64 Embedding**: Embed QR in documents
- **Verification**: Scan to verify letter authenticity
- **Custom Data**: Include letter metadata in QR

## Best Practices

1. Always use `Modules\Correspondence\Models\Correspondence` or adapter
2. Reference views with module namespace: `correspondence::view-name`
3. Keep business logic in Services directory
4. Use Form Requests for validation
5. Follow Laravel naming conventions
6. Validate file uploads for type and size
7. Use QR codes for letter verification

## Security Considerations

- All routes require authentication
- Tenant isolation is enforced
- Module access middleware protects routes
- File uploads are validated
- Sensitive information protected by confidentiality levels
- CSRF protection on all forms

## Testing

Run module tests:

```bash
php artisan test --filter Correspondence
```

## Future Enhancements

- Digital signature integration
- Email notification for new letters
- Workflow approval system
- Template management
- Batch operations
- Advanced search with full-text
- Integration with external mail systems
- Mobile app support
- OCR for scanned documents

## License

Proprietary - Internal Use Only
