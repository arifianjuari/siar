<?php

namespace App\Models;

use Modules\Correspondence\Models\Correspondence as BaseCorrespondence;

/**
 * Backward compatibility adapter for Correspondence model
 * 
 * This class extends the module's Correspondence model to maintain
 * backward compatibility with existing code.
 */
class Correspondence extends BaseCorrespondence
{
    // Adapter for backward compatibility
    // All functionality is inherited from Modules\Correspondence\Models\Correspondence
}
