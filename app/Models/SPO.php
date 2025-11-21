<?php

namespace App\Models;

use Modules\SPOManagement\Models\SPO as BaseSPO;

/**
 * Backward compatibility adapter for SPO model.
 * 
 * This class extends the SPOManagement module's SPO model to maintain
 * backward compatibility with existing code that references App\Models\SPO.
 * 
 * @deprecated Use Modules\SPOManagement\Models\SPO instead
 */
class SPO extends BaseSPO
{
    // This class serves as a backward compatibility adapter.
    // All functionality is inherited from Modules\SPOManagement\Models\SPO
}
