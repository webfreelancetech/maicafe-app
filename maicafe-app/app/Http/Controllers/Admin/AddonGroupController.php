<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddonGroup;
use App\Models\ProductAddon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddonGroupController extends Controller
{
    public function index()
    {
        $addonGroups = AddonGroup::withCount('addons')->latest()->paginate(15);
        return view('admin.addons.index', compact('addonGroups'));
    }

    public function create()
    {
        return view('admin.addons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'selection_type' => 'required|in:single,multiple',
            'is_required' => 'boolean',
            'min_selections' => 'nullable|integer|min:0',
            'max_selections' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'addons' => 'required|array|min:1',
            'addons.*.name' => 'required|string|max:255',
            'addons.*.price' => 'required|numeric|min:0',
            'addons.*.is_active' => 'boolean',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $addonGroup = AddonGroup::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'selection_type' => $validated['selection_type'],
                'is_required' => $request->boolean('is_required'),
                'min_selections' => $validated['min_selections'] ?? 0,
                'max_selections' => $validated['max_selections'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            foreach ($request->input('addons') as $index => $addonData) {
                $addonGroup->addons()->create([
                    'name' => $addonData['name'],
                    'price' => $addonData['price'],
                    'is_active' => isset($addonData['is_active']) ? true : true,
                    'sort_order' => $index,
                ]);
            }
        });

        return redirect()->route('admin.addons.index')->with('success', 'Addon group created successfully!');
    }

    public function edit(AddonGroup $addon)
    {
        $addon->load('addons');
        return view('admin.addons.edit', compact('addon'));
    }

    public function update(Request $request, AddonGroup $addon)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'selection_type' => 'required|in:single,multiple',
            'is_required' => 'boolean',
            'min_selections' => 'nullable|integer|min:0',
            'max_selections' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'addons' => 'required|array|min:1',
            'addons.*.id' => 'nullable|integer',
            'addons.*.name' => 'required|string|max:255',
            'addons.*.price' => 'required|numeric|min:0',
            'addons.*.is_active' => 'boolean',
        ]);

        DB::transaction(function () use ($validated, $request, $addon) {
            $addon->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'selection_type' => $validated['selection_type'],
                'is_required' => $request->boolean('is_required'),
                'min_selections' => $validated['min_selections'] ?? 0,
                'max_selections' => $validated['max_selections'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Get existing addon IDs from request
            $existingIds = collect($request->input('addons'))
                ->pluck('id')
                ->filter()
                ->toArray();

            // Delete addons not in the request
            $addon->addons()->whereNotIn('id', $existingIds)->delete();

            // Update or create addons
            foreach ($request->input('addons') as $index => $addonData) {
                $addon->addons()->updateOrCreate(
                    ['id' => $addonData['id'] ?? null],
                    [
                        'name' => $addonData['name'],
                        'price' => $addonData['price'],
                        'is_active' => isset($addonData['is_active']) ? true : true,
                        'sort_order' => $index,
                    ]
                );
            }
        });

        return redirect()->route('admin.addons.index')->with('success', 'Addon group updated successfully!');
    }

    public function destroy(AddonGroup $addon)
    {
        $addon->delete();
        return redirect()->route('admin.addons.index')->with('success', 'Addon group deleted successfully!');
    }
}
