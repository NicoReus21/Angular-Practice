<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Company;
use App\Models\InspectionCategory;
use App\Models\InspectionChecklist;
use App\Traits\ChecksCompanyPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InspectionChecklistController extends Controller
{
    use ChecksCompanyPermission;

    private function categories(): array
    {
        $categories = InspectionCategory::orderBy('sort_order')->get();
        if ($categories->isEmpty()) {
            foreach (InspectionCategory::definitions() as $definition) {
                InspectionCategory::updateOrCreate(
                    ['key' => $definition['key']],
                    [
                        'label' => $definition['label'],
                        'sort_order' => $definition['sort_order'],
                    ]
                );
            }
            $categories = InspectionCategory::orderBy('sort_order')->get();
        }

        return [
            'byKey' => $categories->keyBy('key'),
            'byId' => $categories->keyBy('id'),
        ];
    }

    public function index(Car $car)
    {
        $company = $this->ensureCarCompany($car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission(request(), $company, 'read', 'Inspection'))) {
            return $response;
        }

        return $car->inspectionChecklists()->with('items.category')->latest()->get();
    }

    public function store(Request $request, Car $car)
    {
        $company = $this->ensureCarCompany($car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission($request, $company, 'create', 'Inspection'))) {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'inspected_at' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.category_id' => 'nullable|integer|exists:inspection_categories,id',
            'items.*.key' => 'nullable|string',
            'items.*.value' => 'required|in:yes,no,na',
            'items.*.comment' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $categoryMaps = $this->categories();
        $categoriesByKey = $categoryMaps['byKey'];
        $categoriesById = $categoryMaps['byId'];

        $items = [];
        foreach ($data['items'] as $item) {
            $category = null;
            if (!empty($item['category_id'])) {
                $category = $categoriesById->get((int) $item['category_id']);
            } elseif (!empty($item['key'])) {
                $category = $categoriesByKey->get($item['key']);
            }

            if (!$category) {
                return response()->json(['errors' => ['items' => ['Invalid inspection category.']]], 422);
            }
            if ($item['value'] === 'no' && empty($item['comment'])) {
                return response()->json(['errors' => ['items' => ['Comment is required when value is no.']]], 422);
            }

            $items[] = [
                'inspection_category_id' => $category->id,
                'key' => $category->key,
                'label' => $category->label,
                'value' => $item['value'],
                'comment' => $item['comment'] ?? null,
            ];
        }

        $checklist = InspectionChecklist::create([
            'car_id' => $car->id,
            'inspected_at' => $data['inspected_at'] ?? null,
            'created_by_user_id' => $request->user()?->id,
        ]);

        $checklist->items()->createMany($items);

        return response()->json($checklist->load('items.category'), 201);
    }

    public function show(InspectionChecklist $inspectionChecklist)
    {
        $company = $this->ensureCarCompany($inspectionChecklist->car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission(request(), $company, 'read', 'Inspection'))) {
            return $response;
        }

        return $inspectionChecklist->load('items.category');
    }

    public function update(Request $request, InspectionChecklist $inspectionChecklist)
    {
        $company = $this->ensureCarCompany($inspectionChecklist->car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission($request, $company, 'update', 'Inspection'))) {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'inspected_at' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.category_id' => 'nullable|integer|exists:inspection_categories,id',
            'items.*.key' => 'nullable|string',
            'items.*.value' => 'required|in:yes,no,na',
            'items.*.comment' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $categoryMaps = $this->categories();
        $categoriesByKey = $categoryMaps['byKey'];
        $categoriesById = $categoryMaps['byId'];

        $items = [];
        foreach ($data['items'] as $item) {
            $category = null;
            if (!empty($item['category_id'])) {
                $category = $categoriesById->get((int) $item['category_id']);
            } elseif (!empty($item['key'])) {
                $category = $categoriesByKey->get($item['key']);
            }

            if (!$category) {
                return response()->json(['errors' => ['items' => ['Invalid inspection category.']]], 422);
            }
            if ($item['value'] === 'no' && empty($item['comment'])) {
                return response()->json(['errors' => ['items' => ['Comment is required when value is no.']]], 422);
            }

            $items[] = [
                'inspection_category_id' => $category->id,
                'key' => $category->key,
                'label' => $category->label,
                'value' => $item['value'],
                'comment' => $item['comment'] ?? null,
            ];
        }

        $inspectionChecklist->update([
            'inspected_at' => $data['inspected_at'] ?? $inspectionChecklist->inspected_at,
        ]);

        $inspectionChecklist->items()->delete();
        $inspectionChecklist->items()->createMany($items);

        return response()->json($inspectionChecklist->load('items.category'));
    }

    public function destroy(InspectionChecklist $inspectionChecklist)
    {
        $company = $this->ensureCarCompany($inspectionChecklist->car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission(request(), $company, 'delete', 'Inspection'))) {
            return $response;
        }

        $inspectionChecklist->delete();
        return response()->json(null, 204);
    }

    private function ensureCarCompany(Car $car): ?Company
    {
        if ($car->company_id) {
            return Company::find($car->company_id);
        }

        if ($car->company) {
            $company = Company::firstOrCreate(
                ['name' => $car->company],
                ['code' => Str::slug($car->company, '-')]
            );
            if (!$company->code) {
                $company->update(['code' => Str::slug($car->company, '-')]);
            }
            $car->company_id = $company->id;
            $car->company = $company->name;
            $car->save();
            return $company;
        }

        return null;
    }
}
