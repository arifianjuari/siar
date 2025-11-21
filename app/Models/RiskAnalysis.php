<?php

namespace App\Models;

use Modules\RiskManagement\Models\RiskAnalysis as BaseRiskAnalysis;

/**
 * Backward compatibility adapter for RiskAnalysis model
 * @deprecated Use Modules\RiskManagement\Models\RiskAnalysis instead
 */
class RiskAnalysis extends BaseRiskAnalysis
{
    // All functionality is inherited from BaseRiskAnalysis
    // This adapter exists only for backward compatibility
}
