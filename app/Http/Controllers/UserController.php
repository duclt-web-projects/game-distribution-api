<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];
        $errors = $this->validate($request, $rules);

        if ($errors) {
            return $this->handleError($errors);
        }
        $credentials = $request->only('email', 'password');

        $token = Auth::guard('api')->attempt($credentials);
        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::guard('api')->user();

        return response()->json([
            'access_token' => $token,
            'type_token' => 'bearer',
            'user' => $user,
            'message' => 'Login successful',
        ]);
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ];
        $errors = $this->validate($request, $rules);

        if ($errors) {
            return $this->handleError($errors);
        }

        $user = $this->service->register($request);

        if (!$user) {
            return response()->json([
                'message' => 'User already exists',
            ], 400);
        }

        $token = Auth::guard('api')->login($user);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            'access_token' => $token,
            'type_token' => 'bearer',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'access_token' => Auth::guard('api')->refresh(),
            'type_token' => 'bearer',
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json(auth()->user());
    }
}
