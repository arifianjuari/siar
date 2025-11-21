<?php

namespace App\Models;

use Modules\ActivityManagement\Models\ActivityStatusLog as BaseActivityStatusLog;

/**
 * Backward compatibility adapter for ActivityStatusLog model
 * @deprecated Use Modules\ActivityManagement\Models\ActivityStatusLog instead
 */
class ActivityStatusLog extends BaseActivityStatusLog
{
    // All functionality is inherited from BaseActivityStatusLog
    // This adapter exists only for backward compatibility
}
