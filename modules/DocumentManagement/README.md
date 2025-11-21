# Document Management Module

## Overview

The Document Management module provides comprehensive functionality for managing documents and files within the application. It supports document categorization, tagging, file uploads, version control (revisions), and multi-tenant document isolation.

## Module Structure

```
modules/DocumentManagement/
├── Config/
│   └── config.php              # Module configuration
├── Database/
│   ├── Migrations/             # Database migrations
│   └── Seeders/                # Database seeders
├── Http/
│   ├── Controllers/            # Module controllers
│   │   ├── DocumentController.php
│   │   └── DocumentManagementController.php
│   ├── Middleware/             # Module-specific middleware
│   ├── Requests/               # Form request validation
│   └── routes.php              # Module routes
├── Models/                     # Module models (uses core models)
├── Providers/                  # Service providers
│   └── DocumentManagementServiceProvider.php
├── Resources/
│   ├── Views/                  # Module views
│   │   ├── dashboard.blade.php
│   │   ├── documents/
│   │   ├── documents-by-tag.blade.php
│   │   └── documents-by-type.blade.php
│   └── Assets/                 # Module assets (CSS, JS, images)
├── Services/                   # Business logic services
├── Tests/                      # Module tests
├── module.json                 # Module metadata
└── README.md                   # This file
```

## Features

- **Document CRUD**: Create, read, update, and delete documents
- **File Upload**: Support for various file formats with storage management
- **Document Categories**: Organize documents by category
- **Tagging System**: Add multiple tags to documents for better organization
- **Document Types**: Classify documents by type (internal, external, unit-specific)
- **Document Scopes**: Define document visibility (internal, external, work unit)
- **Version Control**: Revision system for document updates
- **Dashboard**: Overview with statistics and latest documents
- **Filter by Tag**: View all documents with a specific tag
- **Filter by Type**: View documents by type (Risk Reports, Activities, etc.)
- **Tenant Isolation**: Full multi-tenancy support with tenant.document middleware

## Models

This module uses **core application models** that are shared across the application:

- **Document** (`App\Models\Document`): Main document entity
- **Documentable** (`App\Models\Documentable`): Polymorphic relationships
- **DocumentReference** (`App\Models\DocumentReference`): Document references

These models remain in `App\Models\` as they are used by multiple modules including:

- RiskManagement (for risk report attachments)
- ActivityManagement (for activity documents)

## Routes

All routes are prefixed with `/document-management/` and protected by authentication and module access middleware.

### Main Routes:

- `GET /document-management/dashboard` - Dashboard with statistics
- `GET /document-management/documents` - List all documents
- `GET /document-management/documents/create` - Create new document
- `POST /document-management/documents` - Store new document
- `GET /document-management/documents/{document}` - View document details
- `GET /document-management/documents/{document}/edit` - Edit document
- `PUT /document-management/documents/{document}` - Update document
- `DELETE /document-management/documents/{document}` - Delete document

### Special Routes:

- `POST /document-management/documents/{id}/revise` - Create document revision
- `GET /document-management/documents-by-tag/{slug}` - Filter documents by tag
- `GET /document-management/documents-by-type/{type}` - Filter documents by type

## Permissions

The module uses role-based permissions:

- `can_view`: View documents
- `can_create`: Create new documents and revisions
- `can_edit`: Edit existing documents
- `can_delete`: Delete documents

## Custom Middleware

- `tenant.document`: Ensures users can only access documents within their tenant

## Usage in Views

Views can be referenced using the module namespace:

```blade
@extends('document-management::layouts.master')
@include('document-management::partials.header')
```

## File Storage

Documents are stored using Laravel's Storage facade with the following structure:

- Storage path: `storage/app/public/documents/`
- Public path: `public/storage/documents/`
- File naming: Uses original filename with timestamp prefix

## Dependencies

- Laravel Framework 10.x
- PHP 8.1+
- Requires authentication middleware
- Requires tenant middleware (multi-tenancy)
- Laravel Storage for file handling

## Installation

1. Module is auto-discovered via DocumentManagementServiceProvider
2. Run migrations if any: `php artisan migrate`
3. Ensure storage is linked: `php artisan storage:link`
4. Clear cache: `php artisan cache:clear`
5. Module will be available at `/document-management/`

## Differences from Other Modules

Unlike modules like ActivityManagement or RiskManagement, this module:

- **Does NOT move core models** to the module directory
- Uses existing `App\Models\Document`, `App\Models\Documentable`, `App\Models\DocumentReference` models
- These models are shared across multiple modules
- No backward compatibility adapters needed

This design decision ensures that document models remain centralized and accessible to all modules that need document attachment functionality.

## Best Practices

1. Always use `App\Models\Document` directly
2. Reference views with module namespace: `document-management::view-name`
3. Keep business logic in Services directory
4. Use Form Requests for validation
5. Follow Laravel naming conventions
6. Always check file MIME types before upload
7. Implement proper file size limits

## Security Considerations

- All routes require authentication
- Tenant isolation is enforced via middleware
- Permission checks are applied on sensitive operations
- File uploads are validated for type and size
- Documents are tenant-scoped to prevent cross-tenant access

## Testing

Run module tests:

```bash
php artisan test --filter DocumentManagement
```

## Future Enhancements

- Document preview functionality
- Full-text search within documents
- Document sharing between tenants (with permissions)
- Document approval workflow
- Advanced version comparison
- Document expiration and archiving

## License

Proprietary - Internal Use Only
