<?php

namespace App\Policies;

use App\Models\User;

class WorkUnitPolicy extends BasePolicy
{
    /**
     * Module code for work unit management
     */
    protected string $moduleCode = 'work-units';
}
