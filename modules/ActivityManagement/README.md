# Activity Management Module

## Overview

The Activity Management module provides comprehensive functionality for managing activities, tasks, and assignments in the system.

## Module Structure

```
modules/ActivityManagement/
├── Config/
│   └── config.php              # Module configuration
├── Database/
│   ├── Migrations/             # Database migrations
│   └── Seeders/                # Database seeders
├── Http/
│   ├── Controllers/            # Module controllers
│   │   ├── ActionableItemController.php
│   │   ├── ActivityAssigneeController.php
│   │   ├── ActivityCommentController.php
│   │   ├── ActivityController.php
│   │   └── DashboardController.php
│   ├── Middleware/             # Module-specific middleware
│   ├── Requests/               # Form request validation
│   └── routes.php              # Module routes
├── Models/                     # Module models
│   ├── ActionableItem.php
│   ├── Activity.php
│   ├── ActivityAssignee.php
│   ├── ActivityComment.php
│   └── ActivityStatusLog.php
├── Providers/                  # Service providers
│   ├── ActivityManagementServiceProvider.php
│   └── RouteServiceProvider.php
├── Resources/
│   ├── Views/                  # Module views
│   │   ├── actionable_items/
│   │   ├── assignees/
│   │   ├── comments/
│   │   ├── create.blade.php
│   │   ├── dashboard.blade.php
│   │   ├── edit.blade.php
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   └── Assets/                 # Module assets (CSS, JS, images)
├── Services/                   # Business logic services
├── Tests/                      # Module tests
├── module.json                 # Module metadata
└── README.md                   # This file
```

## Features

- **Activity Management**: Create, update, and track activities
- **Task Assignment**: Assign activities to users or work units
- **Comments System**: Threaded comments on activities
- **Actionable Items**: Track actionable items within activities
- **Status Tracking**: Monitor activity status and progress
- **Dashboard**: Overview of all activities and metrics

## Models

- **Activity**: Main activity entity
- **ActivityAssignee**: Manages activity assignments
- **ActivityComment**: Activity comments and replies
- **ActivityStatusLog**: Tracks status changes
- **ActionableItem**: Actionable items within activities

## Routes

All routes are prefixed with `/activity-management/` and protected by authentication and module access middleware.

### Main Routes:

- `GET /activity-management/` - Dashboard
- `GET /activity-management/activities` - List all activities
- `GET /activity-management/activities/create` - Create new activity
- `POST /activity-management/activities` - Store new activity
- `GET /activity-management/activities/{uuid}` - View activity details
- `GET /activity-management/activities/{uuid}/edit` - Edit activity
- `PATCH /activity-management/activities/{uuid}` - Update activity
- `DELETE /activity-management/activities/{uuid}` - Delete activity

### Sub-resource Routes:

- Comments: `/activity-management/activities/{uuid}/comments`
- Assignees: `/activity-management/activities/{uuid}/assignees`
- Actionable Items: `/activity-management/activities/{uuid}/actionable-items`

## Permissions

The module uses role-based permissions:

- `can_view`: View activities
- `can_create`: Create new activities
- `can_edit`: Edit existing activities
- `can_delete`: Delete activities
- `can_export`: Export activity data

## Usage in Views

Views can be referenced using the module namespace:

```blade
@extends('activity-management::layouts.master')
@include('activity-management::partials.header')
```

## Dependencies

- Laravel Framework 10.x
- PHP 8.1+
- Requires authentication middleware
- Requires tenant middleware (multi-tenancy)

## Installation

1. Module is auto-discovered via ActivityManagementServiceProvider
2. Run migrations: `php artisan migrate`
3. Clear cache: `php artisan cache:clear`
4. Module will be available at `/activity-management/`

## Backward Compatibility

For backward compatibility, adapter models are provided in `app/Models/`:

- `App\Models\Activity` extends `Modules\ActivityManagement\Models\Activity`
- `App\Models\ActivityAssignee` extends `Modules\ActivityManagement\Models\ActivityAssignee`
- `App\Models\ActivityComment` extends `Modules\ActivityManagement\Models\ActivityComment`
- `App\Models\ActivityStatusLog` extends `Modules\ActivityManagement\Models\ActivityStatusLog`
- `App\Models\ActionableItem` extends `Modules\ActivityManagement\Models\ActionableItem`

This ensures existing code continues to work while gradually migrating to the new namespace.

## Best Practices

1. Use module models directly: `Modules\ActivityManagement\Models\Activity`
2. Reference views with module namespace: `activity-management::view-name`
3. Keep business logic in Services directory
4. Use Form Requests for validation
5. Follow Laravel naming conventions

## Testing

Run module tests:

```bash
php artisan test --filter ActivityManagement
```

## License

Proprietary - Internal Use Only
