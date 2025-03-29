<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        // GLOBAL SCOPE: hanya aktif jika ada session dan bukan CLI
        static::addGlobalScope('tenant_id', function (Builder $builder) {
            if (
                !app()->runningInConsole() &&
                app()->bound('session') &&
                session()->has('tenant_id')
            ) {
                // Gunakan nama tabel yang lengkap untuk menghindari ambiguitas
                $table = $builder->getModel()->getTable();
                $builder->where($table . '.tenant_id', session('tenant_id'));
            }
        });

        // SAAT CREATE: isi tenant_id otomatis jika belum ada, hanya saat web
        static::creating(function ($model) {
            if (
                !app()->runningInConsole() &&
                app()->bound('session') &&
                session()->has('tenant_id') &&
                !$model->isDirty('tenant_id')
            ) {
                $model->tenant_id = session('tenant_id');
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeTenant($query, $tenantId)
    {
        // Gunakan nama tabel yang lengkap untuk menghindari ambiguitas
        $table = $query->getModel()->getTable();
        return $query->where($table . '.tenant_id', $tenantId);
    }
}
