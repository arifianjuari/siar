# Risk Management Module

## Overview

The Risk Management module provides comprehensive functionality for managing risk reports and risk analysis in the system.

## Module Structure

```
modules/RiskManagement/
├── Config/
│   └── config.php              # Module configuration
├── Database/
│   ├── Migrations/             # Database migrations
│   └── Seeders/                # Database seeders
├── Http/
│   ├── Controllers/            # Module controllers
│   │   ├── RiskAnalysisController.php
│   │   ├── RiskManagementController.php
│   │   └── RiskReportController.php
│   ├── Middleware/             # Module-specific middleware
│   ├── Requests/               # Form request validation
│   └── routes.php              # Module routes
├── Models/                     # Module models
│   ├── RiskAnalysis.php
│   └── RiskReport.php
├── Providers/                  # Service providers
│   └── RiskManagementServiceProvider.php
├── Resources/
│   ├── Views/                  # Module views
│   └── Assets/                 # Module assets (CSS, JS, images)
├── Services/                   # Business logic services
├── Tests/                      # Module tests
├── module.json                 # Module metadata
└── README.md                   # This file
```

## Features

- **Risk Report Management**: Create, update, and track risk reports
- **Risk Analysis**: Perform detailed risk analysis with root cause identification
- **Document Integration**: Link risk reports with documents
- **Activity Integration**: Create activities from risk reports
- **Export Functionality**: Export reports to PDF/Word format
- **QR Code Generation**: Generate QR codes for reports
- **Dashboard**: Overview of all risk reports and metrics

## Models

- **RiskReport**: Main risk report entity with tenant isolation
- **RiskAnalysis**: Risk analysis with root cause and recommendations

## Routes

All routes are prefixed with `/risk-management/` and protected by authentication and module access middleware.

### Main Routes:

- `GET /risk-management/` - Dashboard
- `GET /risk-management/reports` - List all risk reports
- `GET /risk-management/reports/create` - Create new risk report
- `POST /risk-management/reports` - Store new risk report
- `GET /risk-management/reports/{id}` - View risk report details
- `GET /risk-management/reports/{id}/edit` - Edit risk report
- `PUT /risk-management/reports/{id}` - Update risk report
- `DELETE /risk-management/reports/{id}` - Delete risk report

### Risk Analysis Routes:

- `GET /risk-management/reports/{reportId}/analysis` - View analysis
- `GET /risk-management/reports/{reportId}/analysis/create` - Create analysis
- `POST /risk-management/reports/{reportId}/analysis` - Store analysis
- `GET /risk-management/analysis/{id}/edit` - Edit analysis
- `PUT /risk-management/analysis/{id}` - Update analysis

### Export Routes:

- `GET /risk-management/reports/{id}/export-pdf` - Export to PDF
- `GET /risk-management/reports/{id}/laporan-awal` - Initial report
- `GET /risk-management/reports/{id}/laporan-akhir` - Final report

## Permissions

The module uses role-based permissions:

- `can_view`: View risk reports
- `can_create`: Create new risk reports
- `can_edit`: Edit existing risk reports
- `can_delete`: Delete risk reports
- `can_export`: Export risk report data

## Usage in Views

Views can be referenced using the module namespace:

```blade
@extends('risk-management::layouts.master')
@include('risk-management::partials.header')
```

## Dependencies

- Laravel Framework 10.x
- PHP 8.1+
- Requires authentication middleware
- Requires tenant middleware (multi-tenancy)
- DomPDF for PDF generation
- SimpleSoftwareIO/simple-qrcode for QR codes

## Installation

1. Module is auto-discovered via RiskManagementServiceProvider
2. Run migrations: `php artisan migrate`
3. Clear cache: `php artisan cache:clear`
4. Module will be available at `/risk-management/`

## Backward Compatibility

For backward compatibility, adapter models are provided in `app/Models/`:

- `App\Models\RiskReport` extends `Modules\RiskManagement\Models\RiskReport`
- `App\Models\RiskAnalysis` extends `Modules\RiskManagement\Models\RiskAnalysis`

This ensures existing code continues to work while gradually migrating to the new namespace.

## Best Practices

1. Use module models directly: `Modules\RiskManagement\Models\RiskReport`
2. Reference views with module namespace: `risk-management::view-name`
3. Keep business logic in Services directory
4. Use Form Requests for validation
5. Follow Laravel naming conventions

## Testing

Run module tests:

```bash
php artisan test --filter RiskManagement
```

## License

Proprietary - Internal Use Only
