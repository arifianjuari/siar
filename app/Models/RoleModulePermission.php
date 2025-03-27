<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModulePermission extends Model
{
    use HasFactory;

    /**
     * Nama tabel
     */
    protected $table = 'role_module_permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'module_id',
        'can_view',
        'can_create',
        'can_edit',
        'can_delete',
        'can_export',
        'can_import',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'can_view' => 'boolean',
        'can_create' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
        'can_export' => 'boolean',
        'can_import' => 'boolean',
    ];

    /**
     * Get role relatif ke permission
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get module relatif ke permission
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Check if permission has specific ability
     */
    public function hasAbility($ability)
    {
        return $this->{$ability} === true;
    }

    /**
     * Get tenant melalui role
     */
    public function tenant()
    {
        return $this->role->tenant;
    }
}
