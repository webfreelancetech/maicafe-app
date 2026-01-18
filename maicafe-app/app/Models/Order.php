<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'daily_token', 'token_date', 'user_id', 'store_id', 
        'customer_name', 'customer_email', 'customer_phone', 'customer_address',
        'delivery_address', 'order_type', 'subtotal', 'tax', 'delivery_charge',
        'discount', 'coupon_code', 'total', 'status', 'notes',
        'payment_method', 'payment_status', 'payment_reference'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'token_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            // Generate order number if not set
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            }
            
            // Generate daily token
            if (empty($order->daily_token)) {
                $order->token_date = now()->toDateString();
                $order->daily_token = self::generateDailyToken($order->token_date, $order->store_id);
            }
        });
    }

    /**
     * Generate the next daily token for a given date and store.
     */
    public static function generateDailyToken($date, $storeId = null)
    {
        $query = self::where('token_date', $date);
        
        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        
        $lastToken = $query->max('daily_token') ?? 0;
        
        return $lastToken + 1;
    }

    /**
     * Get formatted token display (e.g., "T001", "T042")
     */
    public function getFormattedTokenAttribute()
    {
        if (!$this->daily_token) {
            return null;
        }
        return 'T' . str_pad($this->daily_token, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get token with date prefix (e.g., "18-T001")
     */
    public function getFullTokenAttribute()
    {
        if (!$this->daily_token || !$this->token_date) {
            return null;
        }
        $day = $this->token_date->format('d');
        return $day . '-' . $this->formatted_token;
    }

    /**
     * Scope to get today's orders
     */
    public function scopeToday($query)
    {
        return $query->where('token_date', now()->toDateString());
    }

    /**
     * Scope to get orders by token date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('token_date', $date);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}


