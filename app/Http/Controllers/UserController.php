<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'organization']);

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'ilike', "%{$request->search}%")
                  ->orWhere('email', 'ilike', "%{$request->search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        // Filter by organization
        if ($request->filled('organization')) {
            $query->where('organization_id', $request->organization);
        }

        // Sort
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        
        switch ($sort) {
            case 'name':
                $query->orderBy('name', $direction);
                break;
            case 'email':
                $query->orderBy('email', $direction);
                break;
            case 'role':
                $query->join('roles', 'users.role_id', '=', 'roles.id')
                      ->orderBy('roles.name', $direction)
                      ->select('users.*');
                break;
            case 'organization':
                $query->join('organizations', 'users.organization_id', '=', 'organizations.id')
                      ->orderBy('organizations.name', $direction)
                      ->select('users.*');
                break;
            case 'created_at':
                $query->orderBy('created_at', $direction);
                break;
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $users = $query->paginate(15);

        // Data for filters
        $roles = Role::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();

        // Stats
        $stats = [
            'total' => User::count(),
            'this_month' => User::whereMonth('created_at', now()->month)->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'roles_count' => Role::count(),
        ];

        return view('users.index', compact('users', 'roles', 'organizations', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        
        return view('users.create', compact('roles', 'organizations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'role_id' => 'required|exists:roles,id',
        'organization_id' => 'nullable|exists:organizations,id',
    ]);

    $validated['password'] = Hash::make($validated['password']);

    $user = User::create($validated);

    // Kirim email verification
    event(new Registered($user));

    if ($request->get('action') === 'save_and_new') {
        return redirect()
            ->route('users.create')
            ->with('success', 'User berhasil ditambahkan dan email verifikasi telah dikirim. Silakan tambah user baru.');
    }

    return redirect()
        ->route('users.index')
        ->with('success', 'User berhasil ditambahkan dan email verifikasi telah dikirim.');
}

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['role', 'organization', 'mapsets']);
        
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        
        return view('users.edit', compact('user', 'roles', 'organizations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'organization_id' => 'nullable|exists:organizations,id',
        ]);

        // Only update password if provided
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting current user
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * API endpoint for user suggestions
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where(function($q) use ($query) {
                $q->where('name', 'ilike', "%{$query}%")
                  ->orWhere('email', 'ilike', "%{$query}%");
            })
            ->with(['role', 'organization'])
            ->select('id', 'name', 'email', 'role_id', 'organization_id')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(Request $request, User $user)
    {
        // Prevent disabling current user
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'Cannot disable your own account'], 403);
        }

        $field = $request->get('field', 'is_active');
        
        if (!in_array($field, ['is_active', 'email_verified_at'])) {
            return response()->json(['error' => 'Invalid field'], 400);
        }

        if ($field === 'email_verified_at') {
            $user->update([
                'email_verified_at' => $user->email_verified_at ? null : now()
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => $user->{$field},
            'message' => 'Status berhasil diubah.'
        ]);
    }

    /**
     * Get user statistics
     */
    public function getStats()
    {
        $stats = [
            'total' => User::count(),
            'this_month' => User::whereMonth('created_at', now()->month)->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'by_role' => DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->select('roles.name', DB::raw('count(*) as count'))
                ->groupBy('roles.id', 'roles.name')
                ->orderBy('count', 'desc')
                ->get(),
            'by_organization' => DB::table('users')
                ->join('organizations', 'users.organization_id', '=', 'organizations.id')
                ->select('organizations.name', DB::raw('count(*) as count'))
                ->groupBy('organizations.id', 'organizations.name')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
        ];

        return response()->json($stats);
    }
}