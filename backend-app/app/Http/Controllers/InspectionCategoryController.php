<?php

namespace App\Http\Controllers;

use App\Models\InspectionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InspectionCategoryController extends Controller
{
    public function index()
    {
        return InspectionCategory::orderBy('sort_order')->orderBy('label')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string|max:255',
            'key' => 'nullable|string|max:255|unique:inspection_categories,key',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $key = $data['key'] ?? $this->makeUniqueKey($data['label']);

        $category = InspectionCategory::create([
            'label' => $data['label'],
            'key' => $key,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return response()->json($category, 201);
    }

    public function update(Request $request, InspectionCategory $inspectionCategory)
    {
        $data = $request->validate([
            'label' => 'sometimes|required|string|max:255',
            'key' => 'sometimes|nullable|string|max:255|unique:inspection_categories,key,' . $inspectionCategory->id,
            'sort_order' => 'sometimes|nullable|integer|min:0',
        ]);

        if (array_key_exists('label', $data) && !array_key_exists('key', $data)) {
            $data['key'] = $inspectionCategory->key;
        }

        $inspectionCategory->update($data);

        return response()->json($inspectionCategory);
    }

    public function destroy(InspectionCategory $inspectionCategory)
    {
        $inspectionCategory->delete();
        return response()->json(null, 204);
    }

    private function makeUniqueKey(string $label): string
    {
        $baseKey = Str::slug($label, '_');
        $candidate = $baseKey;
        $counter = 2;

        while (InspectionCategory::where('key', $candidate)->exists()) {
            $candidate = $baseKey . '_' . $counter;
            $counter += 1;
        }

        return $candidate;
    }
}
