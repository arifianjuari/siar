# Work Unit Module

## Overview

The Work Unit module provides comprehensive functionality for managing organizational structure (work units/unit kerja) and Standard Operating Procedures (SPO) documents. This module supports hierarchical unit management, SPO document lifecycle, and dashboard analytics.

## Module Structure

```
modules/WorkUnit/
├── Config/
│   └── config.php              # Module configuration
├── Database/
│   ├── Migrations/             # Database migrations
│   └── Seeders/                # Database seeders
├── Http/
│   ├── Controllers/            # Module controllers
│   │   ├── WorkUnitController.php
│   │   ├── WorkUnitFixController.php
│   │   └── SPOController.php
│   ├── Middleware/             # Module-specific middleware
│   ├── Requests/               # Form request validation
│   └── routes.php              # Module routes
├── Models/                     # Module models (uses core models)
├── Providers/                  # Service providers
│   └── WorkUnitServiceProvider.php
├── Resources/
│   ├── Views/                  # Module views
│   │   ├── dashboard.blade.php
│   │   ├── fixed-index.blade.php
│   │   ├── form.blade.php
│   │   ├── global-dashboard.blade.php
│   │   ├── index.blade.php
│   │   ├── partials/
│   │   └── spo/                # SPO subviews
│   └── Assets/                 # Module assets (CSS, JS, images)
├── Services/                   # Business logic services
├── Tests/                      # Module tests
├── module.json                 # Module metadata
└── README.md                   # This file
```

## Features

### Work Unit Management

- **Hierarchical Structure**: Support for parent-child relationships
- **CRUD Operations**: Create, read, update, delete work units
- **Status Management**: Active/inactive status toggle
- **Order Management**: Reorder units within hierarchy
- **Dashboard**: Unit-specific analytics and metrics
- **Global Dashboard**: Organization-wide overview

### SPO Management

- **Document Lifecycle**: Draft → Approved → Expired → Revision
- **Document Types**: SPO, SOP, IK, Pedoman, Kebijakan, Program
- **Confidentiality Levels**: Public, Internal, Confidential, Restricted
- **Version Control**: Track document versions and revisions
- **Linked Units**: Associate SPO with multiple work units
- **Review Cycle**: Configurable review periods
- **QR Code Generation**: Generate QR codes for SPO documents
- **PDF Export**: Export SPO as formatted PDF documents
- **Dashboard**: SPO analytics and statistics

## Models

### Core Models (Not Moved)

This module uses **core application models** that remain in `App\Models\`:

- **WorkUnit** (`App\Models\WorkUnit`): Work unit/organizational structure
- **SPO** (`App\Models\SPO`): Standard Operating Procedure documents
- **User** (`App\Models\User`): For approvers and creators

These models are shared across multiple modules and remain centralized.

## Routes

All routes are prefixed with `/work-units/` and protected by authentication and permission middleware.

### Work Unit Routes:

- `GET /work-units` - List all work units
- `GET /work-units/create` - Create new work unit form
- `POST /work-units` - Store new work unit
- `GET /work-units/{id}/edit` - Edit work unit form
- `PUT /work-units/{id}` - Update work unit
- `DELETE /work-units/{id}` - Delete work unit
- `GET /work-units/{id}/dashboard` - Work unit dashboard
- `GET /work-units/global-dashboard` - Global dashboard

### SPO Routes:

- `GET /work-units/spo` - List all SPO documents
- `GET /work-units/spo/dashboard` - SPO dashboard
- `GET /work-units/spo/create` - Create new SPO form
- `POST /work-units/spo` - Store new SPO
- `GET /work-units/spo/{id}` - View SPO details
- `GET /work-units/spo/{id}/edit` - Edit SPO form
- `PUT /work-units/spo/{id}` - Update SPO
- `DELETE /work-units/spo/{id}` - Delete SPO
- `GET /work-units/spo/{id}/generate-pdf` - Generate PDF
- `GET /work-units/spo/{id}/qr-code` - Generate QR code

### Debug Routes (Fix):

- `GET /work-units-fix` - Debug listing (no strict permissions)
- `GET /work-units-fix/{id}/dashboard` - Debug dashboard

## Permissions

The module uses permission checks via `check.permission:work-units,can_*`:

- `can_view`: View work units and SPO documents
- `can_create`: Create new work units and SPO documents
- `can_edit`: Edit existing work units and SPO documents
- `can_delete`: Delete work units and SPO documents

## Folder Consolidation

### Previous Issue

There were **three duplicate view folders**:

- `resources/views/modules/WorkUnit/` (PascalCase)
- `resources/views/modules/work_unit/` (snake_case)
- `resources/views/modules/work-unit/` (kebab-case)

### Solution

All views have been consolidated into the module's single location:

- `modules/WorkUnit/Resources/Views/` (includes all views + spo subfolder)

### View References Updated

All controller view references updated to use module namespace:

- Old: `view('modules.WorkUnit.index')`
- Old: `view('modules.work-unit.spo.index')`
- **New**: `view('work-unit::index')`
- **New**: `view('work-unit::spo.index')`

## Usage in Views

Views can be referenced using the module namespace:

```blade
@extends('work-unit::layouts.master')
@include('work-unit::partials.header')
@include('work-unit::spo.show')
```

## Dependencies

- Laravel Framework 10.x
- PHP 8.1+
- Requires authentication middleware
- Requires tenant middleware (multi-tenancy)
- Requires check.permission middleware
- Barryvdh/laravel-dompdf for PDF generation
- SimpleSoftwareIO/simple-qrcode for QR codes

## Installation

1. Module is auto-discovered via WorkUnitServiceProvider
2. Run migrations if any: `php artisan migrate`
3. Clear cache: `php artisan cache:clear`
4. Module will be available at `/work-units/`

## Key Features Detail

### Hierarchical Work Units

- Parent-child relationships
- Recursive hierarchy display
- Flattened hierarchy for listings
- Unit path display (e.g., "Parent > Child > Grandchild")

### SPO Document Management

- **Draft Mode**: Create and edit before approval
- **Approval Workflow**: Approver assignment and validation
- **Status Tracking**: Draft, Approved, Expired, Revision
- **Document Numbers**: Auto-generated or custom
- **Expiration Dates**: Based on review cycle
- **Linked Units**: Multi-unit association
- **Tags**: Categorization support
- **File Attachments**: Document file uploads

### Dashboard Analytics

- Total work units count
- Active vs inactive units
- SPO statistics by type
- SPO statistics by confidentiality
- Recent SPO documents
- Documents needing review
- Top units by SPO count

## Best Practices

1. Always use `App\Models\WorkUnit` and `App\Models\SPO` directly
2. Reference views with module namespace: `work-unit::view-name`
3. Keep business logic in Services directory
4. Use Form Requests for validation
5. Follow Laravel naming conventions
6. Use policies for authorization checks
7. Implement proper error handling for hierarchy operations

## Security Considerations

- All routes require authentication
- Tenant isolation is enforced
- Permission checks are applied via middleware
- SPO documents respect tenant boundaries
- File uploads are validated
- CSRF protection on all forms

## Testing

Run module tests:

```bash
php artisan test --filter WorkUnit
```

## Future Enhancements

- Workflow automation for SPO approval
- Email notifications for expiring SPOs
- Advanced reporting and analytics
- Bulk operations for work units
- SPO template management
- Document comparison between versions
- Integration with document management system
- Mobile-optimized views

## License

Proprietary - Internal Use Only
