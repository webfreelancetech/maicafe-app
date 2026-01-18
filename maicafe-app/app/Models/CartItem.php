<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'product_name',
        'variant_name',
        'unit_price',
        'quantity',
        'addons_total',
        'item_total',
        'addons',
        'special_instructions',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'addons_total' => 'decimal:2',
        'item_total' => 'decimal:2',
        'addons' => 'array',
    ];

    /**
     * Get the cart that owns this item.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant.
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Calculate and update item total.
     */
    public function calculateTotal()
    {
        $this->item_total = ($this->unit_price + $this->addons_total) * $this->quantity;
        return $this;
    }

    /**
     * Update quantity and recalculate total.
     */
    public function updateQuantity($quantity)
    {
        $this->quantity = $quantity;
        $this->calculateTotal();
        $this->save();
        return $this;
    }

    /**
     * Get formatted addons for display.
     */
    public function getFormattedAddonsAttribute()
    {
        if (empty($this->addons)) {
            return [];
        }

        return collect($this->addons)->map(function ($addon) {
            return [
                'id' => $addon['id'] ?? null,
                'name' => $addon['name'] ?? $addon['addon_name'] ?? 'Unknown',
                'price' => (float) ($addon['price'] ?? 0),
            ];
        })->toArray();
    }
}
