<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request) {
        $rules = [
            'email'    => 'required|email',
            'password' => 'required'
        ];
        $validator = Validator::make($request->all(),$rules,[
            'email.required' => 'The email is required',
            'email.email' => 'The email is invalid',
            'password.required' => 'The password is required'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validation errors',
                'validate' => $validator->errors(),
                'status' => 'failed'
            ],422);
        }

        $user = User::with(['company'])->where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return (new UserResource($user))->additional([
            'token' => $token,
        ]);
    }


    public function register(Request $request) {

        $request->validate([
            'firstname'       => 'required|string|max:255',
            'lastname'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:6',
            'company_id' => 'required|exists:companies,id',
            'role'       => 'required|in:Admin,Manager,Employee'
        ]);

        $user = User::create([
            'firstname'       => $request->firstname,
            'lastname'       => $request->lastname,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'company_id' => $request->company_id,
            'role'       => $request->role,
        ]);

        return new UserResource($user);
    }
}
