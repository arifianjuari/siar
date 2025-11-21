<?php

namespace App\Models;

use Modules\RiskManagement\Models\RiskReport as BaseRiskReport;

/**
 * Backward compatibility adapter for RiskReport model
 * @deprecated Use Modules\RiskManagement\Models\RiskReport instead
 */
class RiskReport extends BaseRiskReport
{
    // All functionality is inherited from BaseRiskReport
    // This adapter exists only for backward compatibility
}
