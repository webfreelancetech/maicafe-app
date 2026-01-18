<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAddon extends Model
{
    protected $fillable = [
        'addon_group_id',
        'name',
        'price',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Get the addon group that owns this addon.
     */
    public function addonGroup()
    {
        return $this->belongsTo(AddonGroup::class);
    }
}
