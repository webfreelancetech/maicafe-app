<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'store_id',
        'coupon_code',
        'discount_amount',
        'notes',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the store for the cart.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get cart items.
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get or create cart for user.
     */
    public static function getOrCreateForUser($userId)
    {
        return self::firstOrCreate(['user_id' => $userId]);
    }

    /**
     * Get subtotal (sum of all item totals).
     */
    public function getSubtotalAttribute()
    {
        return $this->items->sum('item_total');
    }

    /**
     * Get tax amount.
     */
    public function getTaxAttribute()
    {
        $taxRate = (float) Setting::get('tax_rate', 0);
        return $this->subtotal * ($taxRate / 100);
    }

    /**
     * Get total items count.
     */
    public function getItemsCountAttribute()
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get grand total.
     */
    public function getGrandTotalAttribute()
    {
        return $this->subtotal + $this->tax - $this->discount_amount;
    }

    /**
     * Check if cart is empty.
     */
    public function isEmpty()
    {
        return $this->items->count() === 0;
    }

    /**
     * Clear all items from cart.
     */
    public function clear()
    {
        $this->items()->delete();
        $this->update([
            'coupon_code' => null,
            'discount_amount' => 0,
            'notes' => null,
        ]);
    }
}
