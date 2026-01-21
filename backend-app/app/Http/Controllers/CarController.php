<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Company;
use App\Traits\ChecksCompanyPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Str;

class CarController extends Controller
{
    use ChecksCompanyPermission;
    /**
     * Muestra una lista de todas las unidades.
     * GET /api/cars
     */
    public function index()
    {
        $query = Car::with('maintenances', 'checklists.items', 'documents', 'inspectionChecklists.items')
            ->orderBy('name', 'asc');

        $user = request()->user();
        if ($user) {
            $companyIds = $this->getAllowedCompanyIds($user, 'read');
            if (is_array($companyIds)) {
                $query->whereIn('company_id', $companyIds);
            }
        }

        $cars = $query->get();

        return response()->json($cars);
    }

    /**
     * Almacena una nueva unidad.
     * POST /api/cars
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'plate'   => 'required|string|unique:cars,plate',
            'model'   => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'company_id' => 'nullable|exists:companies,id',
            'status'  => 'required|string|max:255',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $company = $this->resolveCompany($validatedData);
        if (!$company) {
            return response()->json(['errors' => ['company' => ['La compania es requerida.']]], 422);
        }

        if ($response = $this->forbidIfNoCompanyPermission($request, $company, 'create')) {
            return $response;
        }
    
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('car_images', 'public');
            $url = Storage::disk('public')->url($path);
            $validatedData['imageUrl'] = $url;
        }
        unset($validatedData['image']);
        $car = Car::create($validatedData);
        
        return response()->json($car->load('maintenances', 'checklists.items', 'documents', 'inspectionChecklists.items'), 201);
    }

    public function show(Car $car)
    {
        $company = $this->ensureCarCompany($car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission(request(), $company, 'read'))) {
            return $response;
        }

        return $car->load('inspectionChecklists.items');
    }

    /**
     * Actualiza una unidad (carro) existente.
     * PUT/PATCH /api/cars/{id}
     */
    public function update(Request $request, Car $car)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:255',
            'plate'    => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('cars')->ignore($car->id),
            ],
            'model'    => 'nullable|string|max:255',
            'company'  => 'nullable|string|max:255',
            'company_id' => 'nullable|exists:companies,id',
            'status'   => 'sometimes|required|string|max:255',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $targetCompany = array_key_exists('company', $validatedData) || array_key_exists('company_id', $validatedData)
            ? $this->resolveCompany($validatedData)
            : $this->ensureCarCompany($car);

        if ($targetCompany && ($response = $this->forbidIfNoCompanyPermission($request, $targetCompany, 'update'))) {
            return $response;
        }

        if ($request->hasFile('image')) {
            if ($car->imageUrl) {
                $oldPath = ltrim(str_replace('/storage', '', parse_url($car->imageUrl, PHP_URL_PATH)), '/');
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('car_images', 'public');
            $validatedData['imageUrl'] = Storage::disk('public')->url($path);
        }

        unset($validatedData['image']);

        $car->update($validatedData);
        
        return response()->json($car->load('inspectionChecklists.items'));
    }

    public function destroy(Car $car)
    {
        $company = $this->ensureCarCompany($car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission(request(), $company, 'delete'))) {
            return $response;
        }

        if ($car->imageUrl) {
            $path = ltrim(str_replace('/storage', '', parse_url($car->imageUrl, PHP_URL_PATH)), '/');
            Storage::disk('public')->delete($path);
        }
        
        $car->delete();

        return response()->json(null, 204);
    }

    private function resolveCompany(array &$validatedData): ?Company
    {
        if (!empty($validatedData['company_id'])) {
            $company = Company::find($validatedData['company_id']);
            if ($company) {
                $validatedData['company'] = $company->name;
            }
            return $company;
        }

        if (!empty($validatedData['company'])) {
            $name = $validatedData['company'];
            $company = Company::firstOrCreate(
                ['name' => $name],
                ['code' => Str::slug($name, '-')]
            );
            if (!$company->code) {
                $company->update(['code' => Str::slug($name, '-')]);
            }
            $validatedData['company_id'] = $company->id;
            $validatedData['company'] = $company->name;
            return $company;
        }

        return null;
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

    private function getAllowedCompanyIds($user, string $action): ?array
    {
        if ($user->hasPermission('Material Mayor', 'Car', $action)) {
            return null;
        }

        if ($user->hasPermission('Material Mayor', 'Car:all', $action)) {
            return null;
        }

        $sections = $user->getPermissionSectionsByPrefix('Material Mayor', $action, 'Car:');
        $codes = [];
        foreach ($sections as $section) {
            $parts = explode(':', $section, 2);
            if (count($parts) === 2) {
                $codes[] = $parts[1];
            }
        }
        $codes = array_values(array_unique(array_filter($codes)));
        if (in_array('all', $codes, true)) {
            return null;
        }
        if (empty($codes)) {
            return [];
        }

        return Company::query()
            ->whereIn('code', $codes)
            ->orWhereIn('name', $codes)
            ->pluck('id')
            ->all();
    }
}
