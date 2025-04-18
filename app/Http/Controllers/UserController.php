<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Constant;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $admin = $request->user();

        // Restrict access to Admins
        if ($admin->role !== 'Admin') {
            return response()->apiError(Constant::AUTHORIZATION_ERROR,'Unauthorized',403);
        }

        $perPage = $request->input('per_page', 10);

        $users = User::with(['company'])
            ->where('company_id',$admin->company_id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return UserResource::collection($users);
    }

    public function store(Request $request)
    {
        $admin = $request->user();
        // Restrict access to Admins
        if ($admin->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validation
        $validatedData = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:Manager,Employee',
        ]);


        $validatedData['company_id'] = $admin->company_id;
        $validatedData['password']   = Hash::make($validatedData['password']);

        $user = User::create($validatedData);
        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $admin = $request->user();
        // Restrict access to Admins
        if ($admin->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Ensure the user to update belongs to the same company as the Admin
        $user = User::where('id', $id)
            ->where('company_id', $admin->company_id)
            ->firstOrFail();

        // Validate
        $data = $request->validate([
            'role' => 'required|in:Manager,Employee',
        ]);

        $user->role = $data['role'];
        $user->save();

        return response()->json($user, 200);
    }
}
