<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
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
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => [
                'user' => $request->user()
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image) {
                Storage::delete('public/profiles/' . $user->profile_image);
            }
            
            $imageName = time() . '.' . $request->profile_image->extension();
            $request->profile_image->storeAs('public/profiles', $imageName);
            $user->profile_image = $imageName;
        }

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => $user
            ]
        ]);
    }
     /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'email'],
            ]);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status !== Password::RESET_LINK_SENT) {
                return response()->json([
                    'status' => false,
                    'message' => __($status),
                    'data' => null
                ], 422);
            }

            return response()->json([
                'status' => true,
                'message' => 'Password reset link sent successfully',
                'data' => [
                    'email' => $request->email
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'data' => [
                    'errors' => $e->errors()
                ]
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => null
            ], 500);
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => ['required'],
                'email' => ['required', 'email'],
                'password' => ['required', 'confirmed', PasswordRule::defaults()],
            ]);

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($user));
                    
                    // Generate new token for immediate login
                    $token = $user->createToken('auth_token')->plainTextToken;
                    
                    return ['user' => $user, 'token' => $token];
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                $user = User::where('email', $request->email)->first();
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'message' => 'Password reset successful',
                    'data' => [
                        'user' => $user,
                        'token' => $token
                    ]
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => __($status),
                'data' => null
            ], 422);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'data' => [
                    'errors' => $e->errors()
                ]
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => null
            ], 500);
        }
    }
}
