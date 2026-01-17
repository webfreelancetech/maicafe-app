<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Store;
use App\Models\Banner;
use App\Models\Setting;
use Illuminate\Http\Request;

class EcommerceController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->limit(8)
            ->get();

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        $banners = Banner::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        $currencySymbol = Setting::get('currency_symbol', '£');

        return view('ecommerce.index', compact('featuredProducts', 'categories', 'banners', 'currencySymbol'));
    }

    public function menu(Request $request)
    {
        $query = Product::where('is_active', true);

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('sort_order', 'asc')->paginate(12);
        $categories = Category::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        $currencySymbol = Setting::get('currency_symbol', '£');

        return view('ecommerce.menu', compact('products', 'categories', 'currencySymbol'));
    }

    public function product($slug)
    {
        $product = Product::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        $currencySymbol = Setting::get('currency_symbol', '£');

        return view('ecommerce.product', compact('product', 'relatedProducts', 'currencySymbol'));
    }

    public function stores()
    {
        $stores = Store::where('is_active', true)->get();
        return view('ecommerce.stores', compact('stores'));
    }

    public function cart()
    {
        $currencySymbol = Setting::get('currency_symbol', '£');
        $taxRate = (float) Setting::get('tax_rate', 0);
        return view('ecommerce.cart', compact('currencySymbol', 'taxRate'));
    }
}


