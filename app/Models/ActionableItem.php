<?php

namespace App\Models;

use Modules\ActivityManagement\Models\ActionableItem as BaseActionableItem;

/**
 * Backward compatibility adapter for ActionableItem model
 * @deprecated Use Modules\ActivityManagement\Models\ActionableItem instead
 */
class ActionableItem extends BaseActionableItem
{
    // All functionality is inherited from BaseActionableItem
    // This adapter exists only for backward compatibility
}
