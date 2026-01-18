<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'address',
        'loyalty_points',
        'loyalty_tier',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get all orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get completed orders for the user.
     */
    public function completedOrders()
    {
        return $this->hasMany(Order::class)->where('status', 'completed');
    }

    /**
     * Get total amount spent by the user.
     */
    public function getTotalSpentAttribute()
    {
        return $this->orders()->where('status', 'completed')->sum('total');
    }

    /**
     * Get average order value.
     */
    public function getAverageOrderValueAttribute()
    {
        $completedOrders = $this->orders()->where('status', 'completed');
        $count = $completedOrders->count();
        return $count > 0 ? $completedOrders->sum('total') / $count : 0;
    }

    /**
     * Get user's wishlist items.
     */
    public function wishlistItems()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get products in user's wishlist.
     */
    public function wishlist()
    {
        return $this->belongsToMany(Product::class, 'wishlists')
            ->withTimestamps();
    }

    /**
     * Get wishlist count.
     */
    public function getWishlistCountAttribute()
    {
        return $this->wishlistItems()->count();
    }
}
