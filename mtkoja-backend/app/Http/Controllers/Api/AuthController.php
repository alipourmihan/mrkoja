<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,business_owner',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        
        // Revoke all existing tokens
        $user->tokens()->delete();
        
        // Create new token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    public function getAllUsers(Request $request)
    {
        try {
            $users = User::select([
                'id', 'name', 'email', 'role', 'phone', 'address', 
                'gender', 'department', 'notes', 'status', 'avatar', 
                'last_login_at', 'created_at', 'updated_at'
            ])->get();

            // Add full URL for avatars
            $users->transform(function ($user) {
                if ($user->avatar) {
                    $user->avatar_url = asset('storage/avatars/' . $user->avatar);
                }
                return $user;
            });

            return response()->json([
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserBusinesses(Request $request, User $user)
    {
        // For testing purposes, return dummy businesses
        $businesses = [
            [
                'id' => 1,
                'name' => 'رستوران سنتی',
                'description' => 'رستوران سنتی با غذاهای ایرانی',
                'status' => 'approved',
                'created_at' => '2025-09-28T15:00:00.000000Z',
                'category' => [
                    'id' => 1,
                    'name' => 'رستوران'
                ]
            ],
            [
                'id' => 2,
                'name' => 'کافه مدرن',
                'description' => 'کافه مدرن با قهوه‌های عالی',
                'status' => 'pending',
                'created_at' => '2025-09-28T14:00:00.000000Z',
                'category' => [
                    'id' => 2,
                    'name' => 'کافه'
                ]
            ]
        ];

        return response()->json([
            'businesses' => $businesses
        ]);
    }

    public function createUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'role' => 'required|in:user,business_owner,admin',
                'status' => 'sometimes|in:active,inactive,suspended',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'gender' => 'nullable|in:male,female,other',
                'department' => 'nullable|string|max:100',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => $request->status ?? 'active',
            ];

            // Add optional fields if provided
            if ($request->has('gender')) {
                $userData['gender'] = $request->gender;
            }
            if ($request->has('department')) {
                $userData['department'] = $request->department;
            }
            if ($request->has('notes')) {
                $userData['notes'] = $request->notes;
            }

            $user = User::create($userData);

            // Handle avatar upload if provided
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $filename = time() . '_' . $avatar->getClientOriginalName();
                $avatar->storeAs('public/avatars', $filename);
                $user->avatar = $filename;
                $user->save();
            }

            // Create personal access token for the user
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUser(Request $request, User $user)
    {
        // Add full URL for avatar
        if ($user->avatar) {
            $user->avatar_url = asset('storage/avatars/' . $user->avatar);
        }

        return response()->json([
            'user' => $user
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:6',
            'role' => 'sometimes|required|in:user,business_owner,admin',
            'status' => 'sometimes|required|in:active,inactive,suspended',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'gender' => 'nullable|in:male,female,other',
            'department' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only([
            'name', 'email', 'role', 'status', 'phone', 'address', 
            'gender', 'department', 'notes'
        ]);

        // Only update password if provided
        if ($request->has('password') && $request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Handle avatar upload if provided
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '_' . $avatar->getClientOriginalName();
            $avatar->storeAs('public/avatars', $filename);
            $user->avatar = $filename;
            $user->save();
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function deleteUser(Request $request, User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
