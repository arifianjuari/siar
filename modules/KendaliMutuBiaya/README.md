# Kendali Mutu Biaya Module

## Overview

The Kendali Mutu Biaya (Quality Control & Cost Management) module provides comprehensive functionality for managing Clinical Pathways, including quality control evaluation, cost tracking, tariff management, and compliance monitoring.

## Module Structure

```
modules/KendaliMutuBiaya/
├── Config/
│   └── config.php              # Module configuration
├── Database/
│   ├── Migrations/             # Database migrations
│   └── Seeders/                # Database seeders
├── Http/
│   ├── Controllers/            # Module controllers
│   │   └── KendaliMutuBiayaController.php
│   ├── Middleware/             # Module-specific middleware
│   ├── Requests/               # Form request validation
│   └── routes.php              # Module routes
├── Models/                     # Module models
│   ├── ClinicalPathway.php
│   ├── CpStep.php
│   ├── CpTariff.php
│   ├── CpEvaluation.php
│   ├── CpEvaluationStep.php
│   └── CpEvaluationAdditionalStep.php
├── Providers/                  # Service providers
│   └── KendaliMutuBiayaServiceProvider.php
├── Resources/
│   ├── Views/                  # Module views
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   ├── evaluate-cp.blade.php
│   │   ├── manage-steps.blade.php
│   │   ├── manage-tariffs.blade.php
│   │   ├── manage-indicators.blade.php
│   │   ├── show-evaluation.blade.php
│   │   ├── rekap-evaluation.blade.php
│   │   └── pdf.blade.php
│   └── Assets/                 # Module assets (CSS, JS, images)
├── Services/                   # Business logic services
├── Tests/                      # Module tests
├── module.json                 # Module metadata
└── README.md                   # This file
```

## Features

### Clinical Pathway Management

- **CRUD Operations**: Create, read, update, delete clinical pathways
- **Structured Steps**: Define sequential steps in the pathway
- **Status Tracking**: Draft, Published, Archived statuses
- **Version Control**: Track pathway versions
- **Tenant Scoping**: Multi-tenancy support
- **Soft Deletes**: Safe deletion with recovery

### Step Management

- **Sequential Steps**: Ordered pathway steps
- **Step Details**: Description, action required, responsible party
- **Unit Cost**: Cost tracking per step
- **Time Duration**: Expected duration for each step
- **Dependency Tracking**: Step prerequisites

### Tariff Management

- **Claim Values**: INA-CBG claim value tracking
- **Cost Comparison**: Compare actual vs. claim costs
- **Multiple Tariffs**: Support for multiple tariff versions
- **Historical Data**: Track tariff changes over time

### Quality Control Evaluation

- **CP Evaluation**: Evaluate pathway implementation
- **Step Compliance**: Track compliance for each step
- **Additional Steps**: Record unplanned steps
- **Compliance Percentage**: Calculate overall compliance
- **Cost Variance**: Track cost overruns/savings
- **Evaluation Reports**: Comprehensive evaluation reports

### Reports & Analytics

- **Evaluation Recap**: Summary of all evaluations
- **Compliance Metrics**: Quality control metrics
- **Cost Analysis**: Financial performance analysis
- **PDF Export**: Generate printable reports
- **Dashboard**: Visual analytics and trends

## Models

### 1. ClinicalPathway

Located in `Modules\KendaliMutuBiaya\Models\ClinicalPathway`

**Attributes:**

- `tenant_id` - Foreign key to tenant
- `name` - Pathway name
- `code` - Unique pathway code
- `disease_category` - Disease category/ICD code
- `effective_date` - Start date of pathway
- `expiry_date` - End date of pathway
- `status` - draft, published, archived
- `structured_data` - JSON data for additional metadata
- `created_by` - Creator user ID
- `updated_by` - Last updater user ID

**Relationships:**

- `tenant()` - Belongs to Tenant
- `creator()` - Belongs to User (created_by)
- `updater()` - Belongs to User (updated_by)
- `steps()` - Has many CpStep
- `tariff()` - Has one CpTariff
- `tariffs()` - Has many CpTariff
- `evaluations()` - Has many CpEvaluation

### 2. CpStep

**Attributes:**

- `clinical_pathway_id` - Foreign key
- `step_number` - Sequential order
- `step_name` - Step title
- `step_description` - Detailed description
- `action_required` - Action to be taken
- `responsible_party` - Who is responsible
- `duration_hours` - Expected duration
- `unit_cost` - Cost per step
- `created_by`, `updated_by`

**Relationships:**

- `clinicalPathway()` - Belongs to ClinicalPathway
- `creator()` - Belongs to User
- `updater()` - Belongs to User
- `evaluationSteps()` - Has many CpEvaluationStep

### 3. CpTariff

**Attributes:**

- `clinical_pathway_id` - Foreign key
- `tariff_year` - Applicable year
- `claim_value` - INA-CBG claim value
- `description` - Tariff description
- `created_by`, `updated_by`

**Relationships:**

- `clinicalPathway()` - Belongs to ClinicalPathway
- `creator()` - Belongs to User
- `updater()` - Belongs to User

### 4. CpEvaluation

**Attributes:**

- `clinical_pathway_id` - Foreign key
- `evaluation_date` - Date of evaluation
- `patient_code` - Patient identifier
- `evaluator_id` - Evaluator user ID
- `compliance_percentage` - Overall compliance %
- `total_cost` - Total pathway cost
- `notes` - Additional notes
- `created_by`, `updated_by`, `reviewed_by`

**Relationships:**

- `clinicalPathway()` - Belongs to ClinicalPathway
- `evaluator()` - Belongs to User
- `creator()` - Belongs to User
- `updater()` - Belongs to User
- `reviewer()` - Belongs to User
- `evaluationSteps()` - Has many CpEvaluationStep
- `additionalSteps()` - Has many CpEvaluationAdditionalStep

### 5. CpEvaluationStep

**Attributes:**

- `cp_evaluation_id` - Foreign key
- `cp_step_id` - Foreign key
- `is_done` - Boolean compliance
- `actual_duration_hours` - Actual time taken
- `actual_cost` - Actual cost incurred
- `notes` - Step-specific notes
- `created_by`, `updated_by`

**Relationships:**

- `evaluation()` - Belongs to CpEvaluation
- `step()` - Belongs to CpStep
- `creator()` - Belongs to User
- `updater()` - Belongs to User

### 6. CpEvaluationAdditionalStep

**Attributes:**

- `cp_evaluation_id` - Foreign key
- `additional_step_name` - Step name
- `additional_step_description` - Description
- `additional_step_cost` - Cost of additional step
- `reason` - Reason for additional step
- `created_by`, `updated_by`

**Relationships:**

- `evaluation()` - Belongs to CpEvaluation
- `creator()` - Belongs to User
- `updater()` - Belongs to User

**Backward Compatibility:**

- All models have backward compatibility adapters in `App\Models\`

## Routes

All routes are prefixed with `/kendali-mutu-biaya/` and protected by authentication and module access middleware.

### Clinical Pathway Routes:

- `GET /kendali-mutu-biaya` - List all clinical pathways
- `GET /kendali-mutu-biaya/create` - Create new pathway form
- `POST /kendali-mutu-biaya/store` - Store new pathway
- `GET /kendali-mutu-biaya/edit/{id}` - Edit pathway form
- `PUT /kendali-mutu-biaya/update/{id}` - Update pathway
- `DELETE /kendali-mutu-biaya/destroy/{id}` - Delete pathway

### Tariff Routes:

- `GET /kendali-mutu-biaya/tariff/{id}` - Manage tariffs for pathway
- `POST /kendali-mutu-biaya/tariff/{id}/store` - Store tariff

### Evaluation Routes:

- `GET /kendali-mutu-biaya/evaluate/{id}` - Evaluate pathway form
- `POST /kendali-mutu-biaya/evaluate/{id}/store` - Store evaluation
- `GET /kendali-mutu-biaya/evaluation/{id}` - View evaluation details
- `GET /kendali-mutu-biaya/rekap` - Evaluation recap/summary

### Export Routes:

- `GET /kendali-mutu-biaya/pdf/{id}` - Generate PDF report

## Permissions

The module uses middleware check:

- `module:kendali-mutu-biaya` - Access to module

## Usage in Views

Views can be referenced using the module namespace:

```blade
@extends('kendali-mutu-biaya::layouts.master')
@include('kendali-mutu-biaya::partials.header')
```

## Business Logic

### Clinical Pathway Creation

1. Define pathway metadata (name, code, disease category)
2. Set effective and expiry dates
3. Add sequential steps with costs and durations
4. Define tariff (INA-CBG claim value)
5. Publish pathway for use

### Evaluation Process

1. Select published clinical pathway
2. Record patient information
3. Evaluate each step (done/not done, actual duration, actual cost)
4. Record additional unplanned steps if any
5. Calculate compliance percentage
6. Calculate cost variance
7. Generate evaluation report

### Quality Metrics

- **Compliance %** = (Steps completed / Total steps) × 100
- **Cost Variance** = Actual cost - Expected cost
- **Time Variance** = Actual duration - Expected duration

## Dependencies

- Laravel Framework 10.x
- PHP 8.1+
- Requires authentication middleware
- Requires tenant middleware (multi-tenancy)
- Requires module:kendali-mutu-biaya middleware
- Barryvdh/laravel-dompdf for PDF generation
- jQuery for dynamic forms
- Chart.js for analytics (if implemented)

## Installation

1. Module is auto-discovered via KendaliMutuBiayaServiceProvider
2. Run migrations if any: `php artisan migrate`
3. Clear cache: `php artisan cache:clear`
4. Module will be available at `/kendali-mutu-biaya/`

## Key Features Detail

### Dynamic Step Management

- Add/remove steps dynamically during pathway creation
- Reorder steps with drag-and-drop (if implemented)
- Clone steps from existing pathways
- Import/export step templates

### Cost Tracking

- Track expected vs. actual costs at step level
- Calculate total pathway cost
- Compare with INA-CBG claim values
- Identify cost overruns/savings

### Compliance Monitoring

- Track step-by-step compliance
- Calculate overall pathway compliance
- Identify frequently missed steps
- Generate compliance reports

### Evaluation Dashboard

- Summary of evaluations by period
- Compliance trends over time
- Cost variance analysis
- Top performing pathways

## Best Practices

1. Always use module namespace for models
2. Reference views with module namespace: `kendali-mutu-biaya::view-name`
3. Keep business logic in Services directory
4. Use Form Requests for validation
5. Document step definitions clearly
6. Regular evaluation of pathways
7. Update tariffs annually
8. Archive outdated pathways

## Security Considerations

- All routes require authentication
- Tenant isolation is enforced
- Module access middleware protects routes
- Soft deletes prevent data loss
- Audit trail via created_by/updated_by
- CSRF protection on all forms

## Testing

Run module tests:

```bash
php artisan test --filter KendaliMutuBiaya
```

## Future Enhancements

- Real-time pathway tracking
- Mobile app for bedside evaluation
- Integration with EMR systems
- Automated compliance alerts
- Advanced analytics with AI predictions
- Pathway recommendation engine
- Multi-language support
- Template marketplace
- Benchmarking against national standards

## License

Proprietary - Internal Use Only
