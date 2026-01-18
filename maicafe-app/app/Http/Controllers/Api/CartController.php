<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductAddon;
use App\Models\Coupon;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Get current user's cart
     */
    public function index(Request $request)
    {
        $cart = Cart::getOrCreateForUser($request->user()->id);
        $cart->load('items.product', 'items.variant');

        return response()->json([
            'success' => true,
            'data' => $this->formatCart($cart)
        ], 200);
    }

    /**
     * Add item to cart
     */
    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'variant_id' => 'nullable|integer',
            'quantity' => 'required|integer|min:1|max:99',
            'addons' => 'nullable|array',
            'addons.*.id' => 'required_with:addons|integer',
            'addons.*.quantity' => 'nullable|integer|min:1',
            'special_instructions' => 'nullable|string|max:500',
        ]);

        // Get product
        $product = Product::where('id', $validated['product_id'])
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or is not available',
            ], 404);
        }

        // Handle variant
        $variant = null;
        $unitPrice = $product->price;
        $variantName = null;

        if ($product->has_variants) {
            if (empty($validated['variant_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product requires a variant selection',
                ], 422);
            }

            $variant = ProductVariant::where('id', $validated['variant_id'])
                ->where('product_id', $product->id)
                ->where('is_active', true)
                ->first();

            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected variant is not available',
                ], 422);
            }

            $unitPrice = $variant->price;
            $variantName = $variant->name;
        }

        // Handle addons
        $addonsTotal = 0;
        $addonsData = [];

        if (!empty($validated['addons'])) {
            foreach ($validated['addons'] as $addonInput) {
                $addon = ProductAddon::where('id', $addonInput['id'])
                    ->where('is_active', true)
                    ->first();

                if ($addon) {
                    $addonQty = $addonInput['quantity'] ?? 1;
                    $addonPrice = $addon->price * $addonQty;
                    $addonsTotal += $addonPrice;

                    $addonsData[] = [
                        'id' => $addon->id,
                        'addon_name' => $addon->name,
                        'price' => (float) $addon->price,
                        'quantity' => $addonQty,
                        'total' => $addonPrice,
                        'group_id' => $addon->addon_group_id,
                    ];
                }
            }
        }

        $cart = Cart::getOrCreateForUser($request->user()->id);

        // Check if same item already exists in cart (same product, variant, and addons)
        $existingItem = $this->findExistingCartItem($cart, $product->id, $variant?->id, $addonsData);

        if ($existingItem) {
            // Update quantity of existing item
            $newQuantity = $existingItem->quantity + $validated['quantity'];
            if ($newQuantity > 99) {
                $newQuantity = 99;
            }
            $existingItem->updateQuantity($newQuantity);

            $cart->load('items.product', 'items.variant');

            return response()->json([
                'success' => true,
                'message' => 'Cart updated - quantity increased',
                'data' => $this->formatCart($cart)
            ], 200);
        }

        // Calculate item total
        $quantity = $validated['quantity'];
        $itemTotal = ($unitPrice + $addonsTotal) * $quantity;

        // Create new cart item
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
            'product_name' => $product->name,
            'variant_name' => $variantName,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'addons_total' => $addonsTotal,
            'item_total' => $itemTotal,
            'addons' => $addonsData,
            'special_instructions' => $validated['special_instructions'] ?? null,
        ]);

        $cart->load('items.product', 'items.variant');

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'data' => $this->formatCart($cart)
        ], 201);
    }

    /**
     * Update cart item quantity
     */
    public function updateItem(Request $request, $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $cart = Cart::getOrCreateForUser($request->user()->id);
        $cartItem = CartItem::where('id', $itemId)
            ->where('cart_id', $cart->id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
            ], 404);
        }

        $cartItem->updateQuantity($validated['quantity']);
        $cart->load('items.product', 'items.variant');

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated',
            'data' => $this->formatCart($cart)
        ], 200);
    }

    /**
     * Remove item from cart
     */
    public function removeItem(Request $request, $itemId)
    {
        $cart = Cart::getOrCreateForUser($request->user()->id);
        $cartItem = CartItem::where('id', $itemId)
            ->where('cart_id', $cart->id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
            ], 404);
        }

        $cartItem->delete();
        $cart->load('items.product', 'items.variant');

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'data' => $this->formatCart($cart)
        ], 200);
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request)
    {
        $cart = Cart::getOrCreateForUser($request->user()->id);
        $cart->clear();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
            'data' => $this->formatCart($cart)
        ], 200);
    }

    /**
     * Get cart items count
     */
    public function count(Request $request)
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();
        
        $count = $cart ? $cart->items->sum('quantity') : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $count,
            ]
        ], 200);
    }

    /**
     * Apply coupon to cart
     */
    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $cart = Cart::getOrCreateForUser($request->user()->id);

        if ($cart->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot apply coupon to empty cart',
            ], 422);
        }

        // Find coupon
        $coupon = Coupon::where('code', strtoupper($validated['coupon_code']))
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code',
            ], 422);
        }

        // Check coupon validity
        if ($coupon->valid_from && $coupon->valid_from > now()) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon is not yet active',
            ], 422);
        }

        if ($coupon->valid_until && $coupon->valid_until < now()) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon has expired',
            ], 422);
        }

        // Check minimum order amount
        $subtotal = $cart->subtotal;
        if ($coupon->minimum_amount && $subtotal < $coupon->minimum_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum order amount of £' . number_format($coupon->minimum_amount, 2) . ' required',
            ], 422);
        }

        // Check usage limit
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon has reached its usage limit',
            ], 422);
        }

        // Calculate discount
        $discountAmount = 0;
        if ($coupon->type === 'percentage') {
            $discountAmount = $subtotal * ($coupon->value / 100);
        } else {
            // Fixed discount
            $discountAmount = min((float) $coupon->value, $subtotal);
        }

        // Apply coupon to cart
        $cart->update([
            'coupon_code' => $coupon->code,
            'discount_amount' => $discountAmount,
        ]);

        $cart->load('items.product', 'items.variant');

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully',
            'data' => $this->formatCart($cart)
        ], 200);
    }

    /**
     * Remove coupon from cart
     */
    public function removeCoupon(Request $request)
    {
        $cart = Cart::getOrCreateForUser($request->user()->id);

        $cart->update([
            'coupon_code' => null,
            'discount_amount' => 0,
        ]);

        $cart->load('items.product', 'items.variant');

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed',
            'data' => $this->formatCart($cart)
        ], 200);
    }

    /**
     * Update cart notes
     */
    public function updateNotes(Request $request)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = Cart::getOrCreateForUser($request->user()->id);
        $cart->update(['notes' => $validated['notes']]);

        return response()->json([
            'success' => true,
            'message' => 'Notes updated',
            'data' => $this->formatCart($cart)
        ], 200);
    }

    /**
     * Set store for cart
     */
    public function setStore(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
        ]);

        $cart = Cart::getOrCreateForUser($request->user()->id);
        $cart->update(['store_id' => $validated['store_id']]);
        $cart->load('store');

        return response()->json([
            'success' => true,
            'message' => 'Store updated',
            'data' => $this->formatCart($cart)
        ], 200);
    }

    /**
     * Find existing cart item with same product, variant, and addons
     */
    protected function findExistingCartItem($cart, $productId, $variantId, $addonsData)
    {
        foreach ($cart->items as $item) {
            if ($item->product_id != $productId) {
                continue;
            }

            if ($item->variant_id != $variantId) {
                continue;
            }

            // Compare addons
            $existingAddons = $item->addons ?? [];
            $existingAddonIds = collect($existingAddons)->pluck('id')->sort()->values()->toArray();
            $newAddonIds = collect($addonsData)->pluck('id')->sort()->values()->toArray();

            if ($existingAddonIds == $newAddonIds) {
                // Check addon quantities match
                $existingAddonQtys = collect($existingAddons)->keyBy('id')->map(fn($a) => $a['quantity'] ?? 1)->toArray();
                $newAddonQtys = collect($addonsData)->keyBy('id')->map(fn($a) => $a['quantity'] ?? 1)->toArray();

                if ($existingAddonQtys == $newAddonQtys) {
                    return $item;
                }
            }
        }

        return null;
    }

    /**
     * Format cart for API response
     */
    protected function formatCart(Cart $cart)
    {
        $taxRate = (float) Setting::get('tax_rate', 0);
        $currencySymbol = Setting::get('currency_symbol', '£');

        $items = $cart->items->map(function ($item) use ($currencySymbol) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_image' => $item->product?->image ? url('storage/' . $item->product->image) : null,
                'variant_id' => $item->variant_id,
                'variant_name' => $item->variant_name,
                'unit_price' => (float) $item->unit_price,
                'quantity' => $item->quantity,
                'addons' => $item->formatted_addons,
                'addons_total' => (float) $item->addons_total,
                'item_total' => (float) $item->item_total,
                'special_instructions' => $item->special_instructions,
            ];
        });

        $subtotal = $cart->subtotal;
        $tax = $cart->tax;
        $discount = (float) $cart->discount_amount;
        $grandTotal = $subtotal + $tax - $discount;

        return [
            'id' => $cart->id,
            'store_id' => $cart->store_id,
            'store' => $cart->store ? [
                'id' => $cart->store->id,
                'name' => $cart->store->name,
                'address' => $cart->store->address,
            ] : null,
            'items' => $items,
            'items_count' => $cart->items_count,
            'coupon_code' => $cart->coupon_code,
            'notes' => $cart->notes,
            'summary' => [
                'subtotal' => (float) $subtotal,
                'tax_rate' => $taxRate,
                'tax' => (float) $tax,
                'discount' => $discount,
                'grand_total' => (float) $grandTotal,
            ],
            'currency' => [
                'symbol' => $currencySymbol,
                'code' => Setting::get('currency_code', 'GBP'),
            ],
        ];
    }
}
