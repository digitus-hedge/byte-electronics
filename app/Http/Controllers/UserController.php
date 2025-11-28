<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

            if ($request->ajax() && $request->has('keyword')) {
                $query->where('name', 'like', '%' . $request->keyword . '%');
                $users = $query->paginate(10);

                return view('users.partials.table', compact('users'))->render(); // return only table HTML
            }

            $users = $query->paginate(10);
            return view('users.index', compact('users'));
       
    }

    // Show form to create a user
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    // Store a new user with the 'user' role
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id'=>'required',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Assign 'user' role
        $user->assignRole('user');

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    // Show form to edit user's permissions
    public function edit(User $user)
    {
        try {
            // Load all roles
            $roles = Role::all();

            // Load the user's roles
            $user->load('roles');
            $userRoles = $user->roles->pluck('name')->toArray();

            // Debug: Log the data to verify
            Log::info('Edit User', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'roles_count' => $roles->count(),
                'user_roles' => $userRoles,
            ]);

            // Return the view with the necessary data
            return view('users.edit', compact('user', 'roles', 'userRoles'));
        } catch (\Exception $e) {
            Log::error('Error loading user edit page', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            return redirect()->route('admin.users.index')->with('error', 'Failed to load user edit page.');
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            // Validate input
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'role' => 'nullable|exists:roles,name',
            ]);

            // Update user details
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Sync role
            if ($request->role) {
                $user->syncRoles([$request->role]);
            } else {
                $user->syncRoles([]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            return redirect()->back()->with('error', 'Failed to update user.');
        }
    }

    // Assign permissions to a user
    public function assignPermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'array',
        ]);

        // Sync permissions
        $user->syncPermissions($request->permissions);

        return redirect()->route('admin.users.index')->with('success', 'Permissions updated successfully.');
    }

    public function destroy(User $user)
    {
        // Get current user
        $currentUser = auth()->user();

        // Check if current user is super admin
        if (!$currentUser->hasRole('admin')) {
            return response()->json([
            'success' => false,
            'message' => 'Only super admin can delete users.'
            ], 403);
        }

        // Prevent deleting self
        if ($currentUser->id === $user->id) {
            return response()->json([
            'success' => false, 
            'message' => 'You cannot delete your own account.'
            ], 403);
        }

        // Prevent deleting super admin
        if ($user->hasRole('admin')) {
            return response()->json([
            'success' => false,
            'message' => 'Super admin account cannot be deleted.'
            ], 403);
        }

        // Soft delete the user
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.'
        ], 200);
    }
}
