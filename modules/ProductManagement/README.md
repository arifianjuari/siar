# Product Management Module

## Overview

The Product Management module provides functionality for managing products and inventory within the application. It supports product CRUD operations, stock management, pricing, and tenant-based product isolation.

## Module Structure

```
modules/ProductManagement/
├── Config/
│   └── config.php              # Module configuration
├── Database/
│   ├── Migrations/             # Database migrations
│   └── Seeders/                # Database seeders
├── Http/
│   ├── Controllers/            # Module controllers
│   │   └── ProductController.php
│   ├── Middleware/             # Module-specific middleware
│   ├── Requests/               # Form request validation
│   └── routes.php              # Module routes
├── Models/                     # Module models
│   └── Product.php
├── Providers/                  # Service providers
│   └── ProductManagementServiceProvider.php
├── Resources/
│   ├── Views/                  # Module views
│   │   └── products/
│   └── Assets/                 # Module assets (CSS, JS, images)
├── Services/                   # Business logic services
├── Tests/                      # Module tests
├── module.json                 # Module metadata
└── README.md                   # This file
```

## Features

- **Product CRUD**: Create, read, update, and delete products
- **Stock Management**: Track product inventory
- **Pricing Management**: Set and update product prices
- **Product Information**: Name, code, SKU, description
- **Product Images**: Upload and manage product images
- **Active Status**: Enable/disable products
- **Tenant Isolation**: Full multi-tenancy support with automatic tenant scoping

## Models

### Product Model

Located in `Modules\ProductManagement\Models\Product`

**Attributes:**

- `tenant_id` - Foreign key to tenant
- `name` - Product name
- `code` - Product code (unique identifier)
- `sku` - Stock Keeping Unit
- `description` - Product description
- `stock` - Current stock quantity
- `price` - Product price (decimal)
- `image` - Product image path
- `is_active` - Active status (boolean)

**Relationships:**

- `tenant()` - Belongs to Tenant

**Scopes:**

- `tenantScope()` - Automatically filters products by current tenant

**Backward Compatibility:**

- A backward compatibility adapter exists at `App\Models\Product` that extends the module's Product model

## Routes

All routes are prefixed with `/product-management/` and protected by authentication and module access middleware.

### Product Routes (Resource):

- `GET /product-management/products` - List all products
- `GET /product-management/products/create` - Create new product form
- `POST /product-management/products` - Store new product
- `GET /product-management/products/{id}` - View product details
- `GET /product-management/products/{id}/edit` - Edit product form
- `PUT /product-management/products/{id}` - Update product
- `DELETE /product-management/products/{id}` - Delete product

## Permissions

The module uses custom permission checks via `PermissionHelper`:

- `can_view`: View products list
- `can_create`: Create new products
- `can_edit`: Edit existing products
- `can_delete`: Delete products

Permissions are enforced in the controller constructor using middleware.

## Usage in Views

Views can be referenced using the module namespace:

```blade
@extends('product-management::layouts.master')
@include('product-management::partials.header')
```

## File Storage

Product images are stored using Laravel's Storage facade:

- Storage path: `storage/app/public/products/`
- Public path: `public/storage/products/`
- File naming: Uses original filename with timestamp

## Dependencies

- Laravel Framework 10.x
- PHP 8.1+
- Requires authentication middleware
- Requires tenant middleware (multi-tenancy)
- Requires module:product-management middleware

## Installation

1. Module is auto-discovered via ProductManagementServiceProvider
2. Run migrations if any: `php artisan migrate`
3. Ensure storage is linked: `php artisan storage:link`
4. Clear cache: `php artisan cache:clear`
5. Module will be available at `/product-management/`

## Tenant Scoping

The Product model automatically applies tenant scoping:

- When creating products, `tenant_id` is automatically set from session
- All queries are automatically filtered by current tenant
- Manual tenant checks are performed in controllers

## Best Practices

1. Always use `tenantScope()` when querying products
2. Reference views with module namespace: `product-management::view-name`
3. Keep business logic in Services directory
4. Use Form Requests for validation
5. Follow Laravel naming conventions
6. Validate image uploads for type and size

## Security Considerations

- All routes require authentication
- Tenant isolation is enforced
- Permission checks are applied via PermissionHelper
- Image uploads are validated
- Products are tenant-scoped to prevent cross-tenant access
- CSRF protection on all forms

## Testing

Run module tests:

```bash
php artisan test --filter ProductManagement
```

## Future Enhancements

- Product categories
- Product variants (size, color, etc.)
- Bulk import/export
- Stock alerts and notifications
- Product reviews and ratings
- Product history and audit log
- Multi-image support
- Integration with e-commerce platforms

## License

Proprietary - Internal Use Only
