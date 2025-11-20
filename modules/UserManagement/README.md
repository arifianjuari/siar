# User Management Module

## Overview

The User Management module provides functionality for managing users and roles within the application. This module handles user creation, role assignment, and permission management at the tenant level.

## Module Structure

```
modules/UserManagement/
├── Config/
│   └── config.php              # Module configuration
├── Database/
│   ├── Migrations/             # Database migrations
│   └── Seeders/                # Database seeders
├── Http/
│   ├── Controllers/            # Module controllers
│   │   ├── UserController.php
│   │   └── RoleController.php
│   ├── Middleware/             # Module-specific middleware
│   ├── Requests/               # Form request validation
│   └── routes.php              # Module routes
├── Models/                     # Module models (uses core models)
├── Providers/                  # Service providers
│   └── UserManagementServiceProvider.php
├── Resources/
│   ├── Views/                  # Module views
│   │   ├── users/
│   │   └── roles/
│   └── Assets/                 # Module assets (CSS, JS, images)
├── Services/                   # Business logic services
├── Tests/                      # Module tests
├── module.json                 # Module metadata
└── README.md                   # This file
```

## Features

- **User Management**: Create, update, view, and delete users
- **Role Management**: Manage roles with module-specific permissions
- **Permission System**: Fine-grained permission control (can_view, can_create, can_edit, can_delete)
- **Work Unit Assignment**: Assign users to work units
- **Multi-tenancy Support**: Full tenant isolation

## Models

This module uses **core application models** that are shared across the application:

- **User** (`App\Models\User`): User model with role and work unit relationships
- **Role** (`App\Models\Role`): Role model with module permissions
- **WorkUnit** (`App\Models\WorkUnit`): Work unit model for organizational structure

## Routes

All routes are prefixed with `/modules/user-management/` and protected by authentication and module access middleware.

### User Routes:

- `GET /modules/user-management/users` - List all users
- `GET /modules/user-management/users/create` - Create new user form
- `POST /modules/user-management/users` - Store new user
- `GET /modules/user-management/users/{id}` - View user details
- `GET /modules/user-management/users/{id}/edit` - Edit user form
- `PUT /modules/user-management/users/{id}` - Update user
- `DELETE /modules/user-management/users/{id}` - Delete user

### Role Routes:

- `GET /modules/user-management/roles` - List all roles
- `GET /modules/user-management/roles/create` - Create new role form
- `POST /modules/user-management/roles` - Store new role
- `GET /modules/user-management/roles/{id}` - View role details
- `GET /modules/user-management/roles/{id}/edit` - Edit role form
- `PUT /modules/user-management/roles/{id}` - Update role
- `DELETE /modules/user-management/roles/{id}` - Delete role

## Permissions

The module uses role-based permissions:

- `can_view`: View users and roles
- `can_create`: Create new users and roles
- `can_edit`: Edit existing users and roles
- `can_delete`: Delete users and roles

## Usage in Views

Views can be referenced using the module namespace:

```blade
@extends('user-management::layouts.master')
@include('user-management::partials.header')
```

## Dependencies

- Laravel Framework 10.x
- PHP 8.1+
- Requires authentication middleware
- Requires tenant middleware (multi-tenancy)

## Installation

1. Module is auto-discovered via UserManagementServiceProvider
2. Run migrations if any: `php artisan migrate`
3. Clear cache: `php artisan cache:clear`
4. Module will be available at `/modules/user-management/`

## Differences from Other Modules

Unlike other modules (e.g., ActivityManagement, RiskManagement), this module:

- **Does NOT move core models** to the module directory
- Uses existing `App\Models\User`, `App\Models\Role`, `App\Models\WorkUnit` models
- These models are shared across the entire application
- No backward compatibility adapters needed

This design decision ensures that core authentication and authorization models remain centralized and accessible to all parts of the application.

## Best Practices

1. Always use `App\Models\User` and `App\Models\Role` directly
2. Reference views with module namespace: `user-management::view-name`
3. Keep business logic in Services directory
4. Use Form Requests for validation
5. Follow Laravel naming conventions

## Testing

Run module tests:

```bash
php artisan test --filter UserManagement
```

## Security Considerations

- All routes require authentication
- Tenant isolation is enforced
- Permission checks are applied on sensitive operations
- Password hashing uses bcrypt
- CSRF protection on all forms

## License

Proprietary - Internal Use Only
