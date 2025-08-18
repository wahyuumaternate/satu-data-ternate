<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Organization::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Sort
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        
        switch ($sort) {
            case 'name':
                $query->orderBy('name', $direction);
                break;
            case 'code':
                $query->orderBy('code', $direction);
                break;
            case 'created_at':
                $query->orderBy('created_at', $direction);
                break;
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $organizations = $query->paginate(10);

        // Stats
        $stats = [
            'total' => Organization::count(),
            'this_month' => Organization::whereMonth('created_at', now()->month)->count(),
            'with_website' => Organization::whereNotNull('website')->count(),
            'with_logo' => Organization::whereNotNull('logo')->count(),
        ];

        return view('organizations.index', compact('organizations', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('organizations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:organizations,code',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB max
        ]);

        // Handle file upload
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . Str::slug($validated['name']) . '.' . $file->getClientOriginalExtension();
            $validated['logo'] = $file->storeAs('organizations', $filename, 'public');
        }

        // Generate code if not provided
        if (empty($validated['code'])) {
            $organization = new Organization();
            $organization->name = $validated['name']; // This will trigger the mutator
            $validated['code'] = $organization->code;
        }

        $organization = Organization::create($validated);

        return redirect()
            ->route('organizations.index')
            ->with('success', 'Organisasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization)
    {
        return view('organizations.show', compact('organization'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization)
    {
        return view('organizations.edit', compact('organization'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('organizations', 'code')->ignore($organization->id)
            ],
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($organization->logo && Storage::disk('public')->exists($organization->logo)) {
                Storage::disk('public')->delete($organization->logo);
            }

            $file = $request->file('logo');
            $filename = time() . '_' . Str::slug($validated['name']) . '.' . $file->getClientOriginalExtension();
            $validated['logo'] = $file->storeAs('organizations', $filename, 'public');
        }

        $organization->update($validated);

        return redirect()
            ->route('organizations.index')
            ->with('success', 'Organisasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        // Delete associated logo file
        if ($organization->logo && Storage::disk('public')->exists($organization->logo)) {
            Storage::disk('public')->delete($organization->logo);
        }

        $organization->delete();

        return redirect()
            ->route('organizations.index')
            ->with('success', 'Organisasi berhasil dihapus.');
    }

    /**
     * API endpoint for organization suggestions
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $organizations = Organization::where('name', 'ilike', "%{$query}%")
            ->select('id', 'name', 'code', 'logo')
            ->limit(10)
            ->get();

        return response()->json($organizations);
    }
}