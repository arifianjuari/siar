<?php

namespace App\Models;

use Modules\PerformanceManagement\Models\PerformanceTemplate as BasePerformanceTemplate;

/**
 * Backward compatibility adapter for PerformanceTemplate model
 * 
 * This class extends the module's PerformanceTemplate model
 * to maintain backward compatibility with existing code.
 */
class PerformanceTemplate extends BasePerformanceTemplate
{
    // Inherit all functionality from module model
}
