<?php

namespace App\Policies;

use App\Models\User;

class CorrespondencePolicy extends BasePolicy
{
    /**
     * Module code for correspondence management
     */
    protected string $moduleCode = 'correspondence';
}
