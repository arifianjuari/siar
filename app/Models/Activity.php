<?php

namespace App\Models;

use Modules\ActivityManagement\Models\Activity as BaseActivity;

/**
 * Backward compatibility adapter for Activity model
 * This class extends the module's Activity model to maintain existing code compatibility
 * @deprecated Use Modules\ActivityManagement\Models\Activity instead
 */
class Activity extends BaseActivity
{
    // All functionality is inherited from BaseActivity
    // This adapter exists only for backward compatibility
}
