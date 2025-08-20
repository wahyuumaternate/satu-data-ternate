<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
     public function __construct()
    {
         $user = Auth::user();
        
        if (!$user->hasRole('super-admin')) {
            abort(403, 'Anda tidak memiliki akses ');
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'organization']);

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'ilike', "%{$request->search}%")
                  ->orWhere('email', 'ilike', "%{$request->search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
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
                $query->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                      ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                      ->where('model_has_roles.model_type', User::class)
                      ->orderBy('roles.name', $direction)
                      ->select('users.*');
                break;
            case 'organization':
                $query->leftJoin('organizations', 'users.organization_id', '=', 'organizations.id')
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

        $users = $query->paginate(15)->withQueryString();

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
    // Basic validation
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|string|exists:roles,name',
        'organization_id' => 'nullable|exists:organizations,id',
    ]);

    // Custom validation: Organisasi wajib untuk role selain super-admin
    if ($validated['role'] !== 'super-admin' && empty($validated['organization_id'])) {
        return back()
            ->withInput()
            ->withErrors(['organization_id' => 'Organisasi wajib dipilih untuk role ' . ucfirst(str_replace('-', ' ', $validated['role']))]);
    }

    $validated['password'] = Hash::make($validated['password']);

    // Remove role dari validated data karena akan di-assign terpisah
    $roleName = $validated['role'];
    unset($validated['role']);

    $user = User::create($validated);

    // Assign role menggunakan Spatie
    $user->assignRole($roleName);

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
        $user->load(['roles', 'organization']);
        
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

   
public function update(Request $request, User $user)
{
    // Basic validation
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
        'role' => 'required|string|exists:roles,name',
        'organization_id' => 'nullable|exists:organizations,id',
    ]);

    // Custom validation: Organisasi wajib untuk role selain super-admin
    if ($validated['role'] !== 'super-admin' && empty($validated['organization_id'])) {
        return back()
            ->withInput()
            ->withErrors(['organization_id' => 'Organisasi wajib dipilih untuk role ' . ucfirst(str_replace('-', ' ', $validated['role']))]);
    }

    // Only update password if provided
    if ($request->filled('password')) {
        $validated['password'] = Hash::make($validated['password']);
    } else {
        unset($validated['password']);
    }

    // Remove role dari validated data
    $roleName = $validated['role'];
    unset($validated['role']);

    $user->update($validated);

    // Sync role (remove old roles and assign new one)
    $user->syncRoles([$roleName]);

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

        // Remove all roles before deleting user
        $user->syncRoles([]);
        
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
            ->with(['roles', 'organization'])
            ->select('id', 'name', 'email', 'organization_id')
            ->limit(10)
            ->get()
            ->map(function($user) {
                // Transform data untuk include role name
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'organization_id' => $user->organization_id,
                    'role_name' => $user->getRoleNames()->first(), // Get first role name
                    'roles' => $user->getRoleNames(), // Get all role names
                    'organization' => $user->organization,
                ];
            });

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
        } else {
            // Assuming you have is_active field in users table
            $user->update([
                'is_active' => !$user->is_active
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
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_type', User::class)
                ->select('roles.name', DB::raw('count(*) as count'))
                ->groupBy('roles.id', 'roles.name')
                ->orderBy('count', 'desc')
                ->get(),
            'by_organization' => DB::table('users')
                ->leftJoin('organizations', 'users.organization_id', '=', 'organizations.id')
                ->select('organizations.name', DB::raw('count(*) as count'))
                ->whereNotNull('organizations.id')
                ->groupBy('organizations.id', 'organizations.name')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        $user->assignRole($request->role);

        return redirect()->back()->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Remove role from user
     */
    public function removeRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        $user->removeRole($request->role);

        return redirect()->back()->with('success', 'Role berhasil dihapus.');
    }

    /**
     * Bulk operations
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,assign_role,remove_role,activate,deactivate',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => 'required_if:action,assign_role,remove_role|exists:roles,name'
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();

        foreach ($users as $user) {
            // Prevent action on current user for certain operations
            if ($user->id === auth()->id() && in_array($request->action, ['delete', 'deactivate'])) {
                continue;
            }

            switch ($request->action) {
                case 'delete':
                    $user->syncRoles([]);
                    $user->delete();
                    break;
                case 'assign_role':
                    $user->assignRole($request->role);
                    break;
                case 'remove_role':
                    $user->removeRole($request->role);
                    break;
                case 'activate':
                    $user->update(['is_active' => true]);
                    break;
                case 'deactivate':
                    $user->update(['is_active' => false]);
                    break;
            }
        }

        return redirect()->back()->with('success', 'Bulk action berhasil dijalankan.');
    }
}