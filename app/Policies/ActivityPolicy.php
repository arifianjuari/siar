<?php

namespace App\Policies;

use App\Models\User;

class ActivityPolicy extends BasePolicy
{
    /**
     * Module code for activity management
     */
    protected string $moduleCode = 'activity-management';
}
