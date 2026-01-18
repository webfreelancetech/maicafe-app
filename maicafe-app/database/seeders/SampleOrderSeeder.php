<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;

class SampleOrderSeeder extends Seeder
{
    /**
     * Create sample orders for user ID 3 with different variants and addons combinations
     */
    public function run()
    {
        $userId = 3;
        $user = User::find($userId);

        if (!$user) {
            $this->command->error("User with ID {$userId} not found!");
            return;
        }

        $this->command->info("Creating sample orders for: {$user->name} ({$user->email})");

        // Order 1: Simple order - Bacon Burger Small variant only
        $order1 = Order::create([
            'order_number' => 'ORD-TEST001',
            'user_id' => $userId,
            'store_id' => 1,
            'order_type' => 'delivery',
            'subtotal' => 15.00,
            'tax' => 1.50,
            'delivery_charge' => 2.00,
            'discount' => 0,
            'total' => 18.50,
            'payment_method' => 'card',
            'payment_status' => 'paid',
            'status' => 'completed',
            'delivery_address' => '123 Test Street, London, UK',
            'notes' => 'Simple order with small burger',
            'created_at' => Carbon::now()->subDays(7),
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => 1,
            'product_name' => 'Bacon Burger',
            'price' => 15.00,
            'quantity' => 1,
            'subtotal' => 15.00,
            'customizations' => [
                'variant' => [
                    'id' => 1,
                    'name' => 'Small',
                    'price' => 15.00
                ],
                'addons' => []
            ]
        ]);

        $this->command->info("Created Order 1: Simple Small Burger");

        // Order 2: Bacon Burger Large + BBQ Chicken with 2 addons
        $order2 = Order::create([
            'order_number' => 'ORD-TEST002',
            'user_id' => $userId,
            'store_id' => 1,
            'order_type' => 'pickup',
            'subtotal' => 54.53,
            'tax' => 5.45,
            'delivery_charge' => 0,
            'discount' => 5.00,
            'total' => 54.98,
            'payment_method' => 'card',
            'payment_status' => 'paid',
            'status' => 'completed',
            'delivery_address' => null,
            'notes' => 'Large burger + chicken with extra shot and vanilla',
            'created_at' => Carbon::now()->subDays(5),
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => 1,
            'product_name' => 'Bacon Burger',
            'price' => 25.00,
            'quantity' => 1,
            'subtotal' => 25.00,
            'customizations' => [
                'variant' => [
                    'id' => 3,
                    'name' => 'Large',
                    'price' => 25.00
                ],
                'addons' => []
            ]
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => 2,
            'product_name' => 'BBQ Chicken Breast',
            'price' => 29.53, // 20 + 4.53 + 5.00
            'quantity' => 1,
            'subtotal' => 29.53,
            'customizations' => [
                'variant' => null,
                'addons' => [
                    [
                        'group_id' => 1,
                        'group_name' => 'Extra Toppings',
                        'addon_id' => 1,
                        'addon_name' => 'Extra Shot',
                        'price' => 4.53
                    ],
                    [
                        'group_id' => 1,
                        'group_name' => 'Extra Toppings',
                        'addon_id' => 4,
                        'addon_name' => 'Vanilla',
                        'price' => 5.00
                    ]
                ]
            ]
        ]);

        $this->command->info("Created Order 2: Large Burger + Chicken with addons");

        // Order 3: Multiple quantities - 2x Medium Burger + 3x BBQ Chicken with different addons
        $order3 = Order::create([
            'order_number' => 'ORD-TEST003',
            'user_id' => $userId,
            'store_id' => 1,
            'order_type' => 'delivery',
            'subtotal' => 115.75,
            'tax' => 11.58,
            'delivery_charge' => 3.00,
            'discount' => 10.00,
            'total' => 120.33,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'preparing',
            'delivery_address' => '789 Demo Avenue, Birmingham, UK',
            'notes' => 'Party order - multiple items',
            'created_at' => Carbon::now()->subDays(2),
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => 1,
            'product_name' => 'Bacon Burger',
            'price' => 20.00,
            'quantity' => 2,
            'subtotal' => 40.00,
            'customizations' => [
                'variant' => [
                    'id' => 2,
                    'name' => 'Medium',
                    'price' => 20.00
                ],
                'addons' => []
            ]
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => 2,
            'product_name' => 'BBQ Chicken Breast',
            'price' => 25.25, // 20 + 3.25 + 2.00
            'quantity' => 3,
            'subtotal' => 75.75,
            'customizations' => [
                'variant' => null,
                'addons' => [
                    [
                        'group_id' => 1,
                        'group_name' => 'Extra Toppings',
                        'addon_id' => 2,
                        'addon_name' => 'Almond Milk',
                        'price' => 3.25
                    ],
                    [
                        'group_id' => 1,
                        'group_name' => 'Extra Toppings',
                        'addon_id' => 3,
                        'addon_name' => 'Caramel',
                        'price' => 2.00
                    ]
                ]
            ]
        ]);

        $this->command->info("Created Order 3: Party order with multiple quantities");

        // Order 4: BBQ Chicken with ALL addons (premium order)
        $allAddonsTotal = 4.53 + 3.25 + 2.00 + 5.00 + 4.00; // 18.78
        $order4 = Order::create([
            'order_number' => 'ORD-TEST004',
            'user_id' => $userId,
            'store_id' => 1,
            'order_type' => 'pickup',
            'subtotal' => 38.78,
            'tax' => 3.88,
            'delivery_charge' => 0,
            'discount' => 0,
            'total' => 42.66,
            'payment_method' => 'card',
            'payment_status' => 'paid',
            'status' => 'completed',
            'delivery_address' => null,
            'notes' => 'Premium order - all toppings',
            'created_at' => Carbon::now()->subDays(3),
        ]);

        OrderItem::create([
            'order_id' => $order4->id,
            'product_id' => 2,
            'product_name' => 'BBQ Chicken Breast',
            'price' => 38.78,
            'quantity' => 1,
            'subtotal' => 38.78,
            'customizations' => [
                'variant' => null,
                'addons' => [
                    [
                        'group_id' => 1,
                        'group_name' => 'Extra Toppings',
                        'addon_id' => 1,
                        'addon_name' => 'Extra Shot',
                        'price' => 4.53
                    ],
                    [
                        'group_id' => 1,
                        'group_name' => 'Extra Toppings',
                        'addon_id' => 2,
                        'addon_name' => 'Almond Milk',
                        'price' => 3.25
                    ],
                    [
                        'group_id' => 1,
                        'group_name' => 'Extra Toppings',
                        'addon_id' => 3,
                        'addon_name' => 'Caramel',
                        'price' => 2.00
                    ],
                    [
                        'group_id' => 1,
                        'group_name' => 'Extra Toppings',
                        'addon_id' => 4,
                        'addon_name' => 'Vanilla',
                        'price' => 5.00
                    ],
                    [
                        'group_id' => 1,
                        'group_name' => 'Extra Toppings',
                        'addon_id' => 5,
                        'addon_name' => 'Hazelnut',
                        'price' => 4.00
                    ]
                ]
            ]
        ]);

        $this->command->info("Created Order 4: Premium order with all addons");

        // Order 5: Mixed order - all 3 burger sizes
        $order5 = Order::create([
            'order_number' => 'ORD-TEST005',
            'user_id' => $userId,
            'store_id' => 1,
            'order_type' => 'delivery',
            'subtotal' => 60.00,
            'tax' => 6.00,
            'delivery_charge' => 2.50,
            'discount' => 0,
            'total' => 68.50,
            'payment_method' => 'card',
            'payment_status' => 'paid',
            'status' => 'pending',
            'delivery_address' => '555 Family Street, Leeds, UK',
            'notes' => 'Family meal - all sizes',
            'created_at' => Carbon::now()->subHours(2),
        ]);

        OrderItem::create([
            'order_id' => $order5->id,
            'product_id' => 1,
            'product_name' => 'Bacon Burger',
            'price' => 15.00,
            'quantity' => 1,
            'subtotal' => 15.00,
            'customizations' => [
                'variant' => [
                    'id' => 1,
                    'name' => 'Small',
                    'price' => 15.00
                ],
                'addons' => []
            ]
        ]);

        OrderItem::create([
            'order_id' => $order5->id,
            'product_id' => 1,
            'product_name' => 'Bacon Burger',
            'price' => 20.00,
            'quantity' => 1,
            'subtotal' => 20.00,
            'customizations' => [
                'variant' => [
                    'id' => 2,
                    'name' => 'Medium',
                    'price' => 20.00
                ],
                'addons' => []
            ]
        ]);

        OrderItem::create([
            'order_id' => $order5->id,
            'product_id' => 1,
            'product_name' => 'Bacon Burger',
            'price' => 25.00,
            'quantity' => 1,
            'subtotal' => 25.00,
            'customizations' => [
                'variant' => [
                    'id' => 3,
                    'name' => 'Large',
                    'price' => 25.00
                ],
                'addons' => []
            ]
        ]);

        $this->command->info("Created Order 5: Family meal with all burger sizes");

        // Order 6: Cancelled order for testing
        $order6 = Order::create([
            'order_number' => 'ORD-TEST006',
            'user_id' => $userId,
            'store_id' => 1,
            'order_type' => 'delivery',
            'subtotal' => 45.00,
            'tax' => 4.50,
            'delivery_charge' => 2.00,
            'discount' => 0,
            'total' => 51.50,
            'payment_method' => 'card',
            'payment_status' => 'refunded',
            'status' => 'cancelled',
            'delivery_address' => '999 Cancel Road, Glasgow, UK',
            'notes' => 'Customer cancelled - changed mind',
            'created_at' => Carbon::now()->subDays(1),
        ]);

        OrderItem::create([
            'order_id' => $order6->id,
            'product_id' => 1,
            'product_name' => 'Bacon Burger',
            'price' => 25.00,
            'quantity' => 1,
            'subtotal' => 25.00,
            'customizations' => [
                'variant' => [
                    'id' => 3,
                    'name' => 'Large',
                    'price' => 25.00
                ],
                'addons' => []
            ]
        ]);

        OrderItem::create([
            'order_id' => $order6->id,
            'product_id' => 2,
            'product_name' => 'BBQ Chicken Breast',
            'price' => 20.00,
            'quantity' => 1,
            'subtotal' => 20.00,
            'customizations' => [
                'variant' => null,
                'addons' => []
            ]
        ]);

        $this->command->info("Created Order 6: Cancelled order");

        $this->command->info("");
        $this->command->info("========================================");
        $this->command->info("Successfully created 6 sample orders for User ID: {$userId}");
        $this->command->info("========================================");
    }
}
