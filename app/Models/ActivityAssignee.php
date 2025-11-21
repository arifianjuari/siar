<?php

namespace App\Models;

use Modules\ActivityManagement\Models\ActivityAssignee as BaseActivityAssignee;

/**
 * Backward compatibility adapter for ActivityAssignee model
 * @deprecated Use Modules\ActivityManagement\Models\ActivityAssignee instead
 */
class ActivityAssignee extends BaseActivityAssignee
{
    // All functionality is inherited from BaseActivityAssignee
    // This adapter exists only for backward compatibility
}
