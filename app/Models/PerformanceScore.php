<?php

namespace App\Models;

use Modules\PerformanceManagement\Models\PerformanceScore as BasePerformanceScore;

/**
 * Backward compatibility adapter for PerformanceScore model
 * 
 * This class extends the module's PerformanceScore model
 * to maintain backward compatibility with existing code.
 */
class PerformanceScore extends BasePerformanceScore
{
    // Inherit all functionality from module model
}
