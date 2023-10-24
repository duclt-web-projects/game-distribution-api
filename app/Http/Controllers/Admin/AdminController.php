<?php

namespace App\Http\Controllers\Admin;

use App\Constants\TokenStatus;
use App\Http\Controllers\BaseController;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends BaseController
{
    public function __construct(AdminService $service)
    {
        $this->service = $service;
    }

    public function test()
    {
        return response()->json('test');
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

        $token = Auth::guard('api_admin')->attempt($credentials);

        if (!$token) {
            return response()->json([
                'message' => 'Email or password is not correct.',
            ], 401);
        }

        $admin = Auth::guard('api_admin')->user();

        return response()->json([
            'admin' => $admin,
            'access_token' => $token,
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

        $admin = $this->service->register($request);

        if (!$admin) {
            return response()->json([
                'message' => 'User already exists',
            ], 400);
        }

        $token = Auth::guard('api_admin')->login($admin);

        return response()->json([
            'admin' => $admin,
            'access_token' => $token,
            'message' => 'Admin created successfully',
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
        try {
            return response()->json([
                'access_token' => Auth::guard('api_admin')->refresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token is Expired. Please login again!!!',
                'status' => TokenStatus::NOT_REFRESH
            ], 401);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json(Auth::guard('api_admin')->user());
    }

    public function edit(Request $request)
    {
        $rules = [
            'name' => 'sometimes|string',
            'date_of_birth' => 'sometimes|string',
            'phone' => 'sometimes|string',
        ];
        $errors = $this->validate($request, $rules);

        if ($errors) {
            return $this->handleError($errors);
        }

        return $this->service->edit($request->all());
    }

    public function uploadAvatar(Request $request)
    {
        $rules = [
            'avatar' => 'required|image',
        ];
        $errors = $this->validate($request, $rules);

        if ($errors) {
            return $this->handleError($errors);
        }

        return $this->service->uploadAvatar();
    }

    public function changePassword(Request $request)
    {
        $rules = [
            'old_password' => 'required|string',
            'new_password' => 'required|string',
        ];
        $errors = $this->validate($request, $rules);

        if ($errors) {
            return $this->handleError($errors);
        }

        return $this->service->changePassword($request->all());
    }
}
