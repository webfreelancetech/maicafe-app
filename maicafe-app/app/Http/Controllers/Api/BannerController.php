<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Get all active banners
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'banners' => $banners->map(function ($banner) {
                    return [
                        'id' => $banner->id,
                        'title' => $banner->title,
                        'subtitle' => $banner->subtitle,
                        'image' => $banner->image ? url('storage/' . $banner->image) : null,
                        'button_text' => $banner->button_text,
                        'button_link' => $banner->button_link,
                        'sort_order' => $banner->sort_order,
                    ];
                })
            ]
        ], 200);
    }

    /**
     * Get a single banner by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $banner = Banner::where('id', $id)
            ->where('is_active', true)
            ->first();

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'banner' => [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'subtitle' => $banner->subtitle,
                    'image' => $banner->image ? url('storage/' . $banner->image) : null,
                    'button_text' => $banner->button_text,
                    'button_link' => $banner->button_link,
                    'sort_order' => $banner->sort_order,
                ]
            ]
        ], 200);
    }
}
