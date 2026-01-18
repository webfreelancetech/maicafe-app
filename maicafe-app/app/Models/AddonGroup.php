<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddonGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'selection_type',
        'is_required',
        'min_selections',
        'max_selections',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the addons for this group.
     */
    public function addons()
    {
        return $this->hasMany(ProductAddon::class)->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get active addons only.
     */
    public function activeAddons()
    {
        return $this->hasMany(ProductAddon::class)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /**
     * Get the products that use this addon group.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_addon_group')
            ->withPivot('sort_order')
            ->withTimestamps();
    }
}
