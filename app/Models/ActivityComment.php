<?php

namespace App\Models;

use Modules\ActivityManagement\Models\ActivityComment as BaseActivityComment;

/**
 * Backward compatibility adapter for ActivityComment model
 * @deprecated Use Modules\ActivityManagement\Models\ActivityComment instead
 */
class ActivityComment extends BaseActivityComment
{
    // All functionality is inherited from BaseActivityComment
    // This adapter exists only for backward compatibility
}
