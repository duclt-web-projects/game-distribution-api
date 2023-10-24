<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminService extends BaseService
{
    public function __construct()
    {
        $this->model = new Admin();
    }

    public function register(Request $request)
    {
        $existAdmin = $this->findBy([['email', '=', $request->get("email")]]);
        if ($existAdmin) return null;

        $admin = $this->model->create([
            'name' => $request->get("name"),
            'email' => $request->get("email"),
            'password' => Hash::make($request->get("password")),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $admin->refresh();
    }

    public function edit($data)
    {
        $user = $this->model->find(Auth::guard('api_admin')->user()->id);

        if (!$user) {
            return response()->json(['message' => "Not found"], 404);
        }

        $data['updated_at'] = now();

        $user->fill($data)->save();

        return $user;
    }

    public function uploadAvatar()
    {
        $user = $this->model->find(Auth::guard('api_admin')->user()->id);

        if (!$user) {
            return response()->json(['message' => "Not found"], 404);
        }

        $fileUpload = upload_image('avatar', 'avatars');

        if (isset($fileUpload['name'])) {
            $fileName = pare_url_file($fileUpload['name'], 'avatars');
            $user->fill(['avatar' => $fileName])->save();
        }

        return $user;
    }

    public function changePassword(array $data)
    {
        $user = $this->model->find(Auth::guard('api_admin')->user()->id);

        if (!$user) {
            return response()->json(['message' => "Not found"], 404);
        }

        $hashedPassword = Auth::guard('api_admin')->user()->getAuthPassword();
        if (!Hash::check($data['old_password'], $hashedPassword)) {
            return response()->json(['message' => "Password is not correct"], 400);
        }

        $user->fill(['password' => Hash::make($data['new_password'])])->save();
        return $user;
    }
}
