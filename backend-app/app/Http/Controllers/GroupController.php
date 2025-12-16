<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index()
    {
        return Group::withCount('user_groups as users_count')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'id_parent_group' => 'nullable|integer|exists:groups,id',
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'id_parent_group' => $validated['id_parent_group'] ?? null,
            'id_user_created' => Auth::id(),
        ]);

        return response()->json($group, 201);
    }

    public function show(string $id)
    {
        return Group::withCount('user_groups as users_count')->findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $group = Group::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'id_parent_group' => 'nullable|integer|exists:groups,id',
        ]);

        $group->update($validated);

        return response()->json($group);
    }

    public function destroy(string $id)
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return response()->json(null, 204);
    }
}
