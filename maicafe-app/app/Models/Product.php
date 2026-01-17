<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'compare_price',
        'image', 'gallery', 'is_featured', 'is_active', 'stock_quantity',
        'sku', 'customization_options', 'sort_order'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
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
}


