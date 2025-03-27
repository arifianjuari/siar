<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

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

    /**
     * Scope untuk hanya mengambil data dari tenant tertentu
     */
    public function scopeTenantScope($query)
    {
        if (session()->has('tenant_id')) {
            return $query->where('tenant_id', session('tenant_id'));
        }

        return $query;
    }

    /**
     * Boot function dari model.
     */
    protected static function boot()
    {
        parent::boot();

        // Set tenant ID otomatis saat membuat produk baru
        static::creating(function ($product) {
            if (session()->has('tenant_id') && empty($product->tenant_id)) {
                $product->tenant_id = session('tenant_id');
            }
        });
    }
}
