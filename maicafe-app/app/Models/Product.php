<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'compare_price',
        'image', 'gallery', 'is_featured', 'is_active', 'has_variants', 'stock_quantity',
        'sku', 'customization_options', 'sort_order'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'has_variants' => 'boolean',
        'gallery' => 'array',
        'customization_options' => 'array',
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the variants for the product.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get active variants only.
     */
    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /**
     * Get the minimum price (considering variants).
     */
    public function getMinPriceAttribute()
    {
        if ($this->has_variants && $this->variants->count() > 0) {
            return $this->variants->where('is_active', true)->min('price');
        }
        return $this->price;
    }

    /**
     * Get the maximum price (considering variants).
     */
    public function getMaxPriceAttribute()
    {
        if ($this->has_variants && $this->variants->count() > 0) {
            return $this->variants->where('is_active', true)->max('price');
        }
        return $this->price;
    }

    /**
     * Get price range display string.
     */
    public function getPriceRangeAttribute()
    {
        if ($this->has_variants && $this->variants->count() > 0) {
            $min = $this->min_price;
            $max = $this->max_price;
            if ($min == $max) {
                return number_format($min, 2);
            }
            return number_format($min, 2) . ' - ' . number_format($max, 2);
        }
        return number_format($this->price, 2);
    }

    /**
     * Get the addon groups for this product.
     */
    public function addonGroups()
    {
        return $this->belongsToMany(AddonGroup::class, 'product_addon_group')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('pivot_sort_order');
    }

    /**
     * Get active addon groups with their active addons.
     */
    public function activeAddonGroups()
    {
        return $this->belongsToMany(AddonGroup::class, 'product_addon_group')
            ->where('addon_groups.is_active', true)
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('pivot_sort_order');
    }
}


