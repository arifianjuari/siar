<?php

namespace App\Models;

use Modules\ProductManagement\Models\Product as BaseProduct;

/**
 * Backward compatibility adapter for Product model
 * 
 * This class extends the module's Product model to maintain
 * backward compatibility with existing code.
 */
class Product extends BaseProduct
{
    // Adapter for backward compatibility
    // All functionality is inherited from Modules\ProductManagement\Models\Product
}
