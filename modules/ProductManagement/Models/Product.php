<?php

namespace Modules\ProductManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tenant;
use App\Traits\BelongsToTenant;

class Product extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'sku',
        'description',
        'stock',
        'price',
        'image',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stock' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi dengan tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    // BelongsToTenant trait now handles:
    // - Automatic tenant_id filtering via global scope
    // - Automatic tenant_id assignment on create
    // - Prevention of tenant_id changes on existing records
    // - Multiple tenant resolution sources (Auth > Session > Request)
}
