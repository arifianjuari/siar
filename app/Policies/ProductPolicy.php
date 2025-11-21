<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ProductPolicy extends BasePolicy
{
    /**
     * Module code for permission checking
     */
    protected string $moduleCode = 'PRODUCT_MANAGEMENT';

    /**
     * Determine if the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        return parent::viewAny($user);
    }

    /**
     * Determine if the user can view the product.
     */
    public function view(User $user, Model $product): bool
    {
        return parent::view($user, $product);
    }

    /**
     * Determine if the user can create products.
     */
    public function create(User $user): bool
    {
        return parent::create($user);
    }

    /**
     * Determine if the user can update the product.
     */
    public function update(User $user, Model $product): bool
    {
        return parent::update($user, $product);
    }

    /**
     * Determine if the user can delete the product.
     */
    public function delete(User $user, Model $product): bool
    {
        return parent::delete($user, $product);
    }

    /**
     * Determine if the user can restore the product.
     */
    public function restore(User $user, Model $product): bool
    {
        return parent::restore($user, $product);
    }

    /**
     * Determine if the user can permanently delete the product.
     */
    public function forceDelete(User $user, Model $product): bool
    {
        return parent::forceDelete($user, $product);
    }

    /**
     * Determine if the user can export products.
     */
    public function export(User $user): bool
    {
        return parent::export($user);
    }

    /**
     * Determine if the user can import products.
     */
    public function import(User $user): bool
    {
        return parent::import($user);
    }
}
