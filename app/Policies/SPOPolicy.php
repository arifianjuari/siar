<?php

namespace App\Policies;

use Modules\SPOManagement\Policies\SPOPolicy as BaseSPOPolicy;

/**
 * Backward compatibility adapter for SPO policy.
 * 
 * This class extends the SPOManagement module's SPOPolicy to maintain
 * backward compatibility with existing code that references App\Policies\SPOPolicy.
 * 
 * @deprecated Use Modules\SPOManagement\Policies\SPOPolicy instead
 */
class SPOPolicy extends BaseSPOPolicy
{
    // This class serves as a backward compatibility adapter.
    // All functionality is inherited from Modules\SPOManagement\Policies\SPOPolicy
}
