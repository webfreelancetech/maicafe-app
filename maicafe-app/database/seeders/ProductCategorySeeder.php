<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    public function run()
    {
        // Create Categories
        $categories = [
            ['name' => 'Hot Drinks', 'slug' => 'hot-drinks', 'sort_order' => 1],
            ['name' => 'Cold Drinks', 'slug' => 'cold-drinks', 'sort_order' => 2],
            ['name' => 'Pastries', 'slug' => 'pastries', 'sort_order' => 3],
            ['name' => 'Sandwiches', 'slug' => 'sandwiches', 'sort_order' => 4],
            ['name' => 'Desserts', 'slug' => 'desserts', 'sort_order' => 5],
        ];

        $createdCategories = [];
        foreach ($categories as $catData) {
            $category = Category::firstOrCreate(
                ['slug' => $catData['slug']],
                [
                    'name' => $catData['name'],
                    'is_active' => true,
                    'sort_order' => $catData['sort_order'],
                ]
            );
            $createdCategories[$catData['slug']] = $category;
        }

        // Create Products
        $products = [
            // Hot Drinks
            [
                'category' => 'hot-drinks',
                'name' => 'Espresso',
                'price' => 2.50,
                'description' => 'Rich and bold espresso shot',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category' => 'hot-drinks',
                'name' => 'Cappuccino',
                'price' => 3.50,
                'description' => 'Espresso with steamed milk and foam',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category' => 'hot-drinks',
                'name' => 'Latte',
                'price' => 4.00,
                'description' => 'Smooth espresso with steamed milk',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category' => 'hot-drinks',
                'name' => 'Americano',
                'price' => 3.00,
                'description' => 'Espresso with hot water',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'category' => 'hot-drinks',
                'name' => 'Mocha',
                'price' => 4.50,
                'description' => 'Espresso with chocolate and steamed milk',
                'is_featured' => true,
                'is_active' => true,
            ],
            // Cold Drinks
            [
                'category' => 'cold-drinks',
                'name' => 'Iced Coffee',
                'price' => 3.50,
                'description' => 'Chilled coffee served over ice',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category' => 'cold-drinks',
                'name' => 'Cold Brew',
                'price' => 4.00,
                'description' => 'Smooth cold-brewed coffee',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category' => 'cold-drinks',
                'name' => 'Iced Latte',
                'price' => 4.50,
                'description' => 'Espresso with cold milk over ice',
                'is_featured' => false,
                'is_active' => true,
            ],
            // Pastries
            [
                'category' => 'pastries',
                'name' => 'Croissant',
                'price' => 2.50,
                'description' => 'Buttery, flaky French croissant',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category' => 'pastries',
                'name' => 'Blueberry Muffin',
                'price' => 3.00,
                'description' => 'Fresh baked muffin with blueberries',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category' => 'pastries',
                'name' => 'Chocolate Chip Cookie',
                'price' => 2.00,
                'description' => 'Classic chocolate chip cookie',
                'is_featured' => false,
                'is_active' => true,
            ],
            // Sandwiches
            [
                'category' => 'sandwiches',
                'name' => 'Turkey & Swiss',
                'price' => 6.50,
                'description' => 'Sliced turkey with Swiss cheese',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category' => 'sandwiches',
                'name' => 'Ham & Cheese',
                'price' => 6.00,
                'description' => 'Classic ham and cheese sandwich',
                'is_featured' => true,
                'is_active' => true,
            ],
            // Desserts
            [
                'category' => 'desserts',
                'name' => 'Cheesecake Slice',
                'price' => 4.50,
                'description' => 'Creamy New York style cheesecake',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category' => 'desserts',
                'name' => 'Chocolate Brownie',
                'price' => 3.50,
                'description' => 'Rich chocolate brownie',
                'is_featured' => true,
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            $category = $createdCategories[$productData['category']];
            Product::firstOrCreate(
                ['slug' => Str::slug($productData['name'])],
                [
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'price' => $productData['price'],
                    'description' => $productData['description'],
                    'is_featured' => $productData['is_featured'],
                    'is_active' => $productData['is_active'],
                    'stock_quantity' => 100,
                    'sort_order' => 0,
                ]
            );
        }

        $this->command->info('✅ Categories and Products seeded successfully!');
    }
}

