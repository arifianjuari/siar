<?php

namespace App\Models;

use Modules\PerformanceManagement\Models\PerformanceIndicator as BasePerformanceIndicator;

/**
 * Backward compatibility adapter for PerformanceIndicator model
 * 
 * This class extends the module's PerformanceIndicator model
 * to maintain backward compatibility with existing code.
 */
class PerformanceIndicator extends BasePerformanceIndicator
{
    // Inherit all functionality from module model
}
