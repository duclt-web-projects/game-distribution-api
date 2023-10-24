<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    public function __construct()
    {
        $this->model = new User();
    }

    public function register(array $data)
    {
        $existUser = $this->findBy([['email', '=', $data["email"]]]);
        if ($existUser) return null;

        $user = $this->model->create([
            'name' => $data["name"],
            'email' => $data["email"],
            'password' => Hash::make($data["password"]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $user->refresh();
    }

    public function loginWithProvider(array $data)
    {
        $existUser = $this->findBy([['email', '=', $data["email"]]]);

        if($existUser) return $existUser;

        $user = $this->model->create([
            'name' => $data["name"],
            'email' => $data["email"],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $user->refresh();
    }

    public function edit($data)
    {
        $user = $this->model->find(auth()->user()->id);

        if (!$user) {
            return response()->json(['message' => "Not found"], 404);
        }

        $data['updated_at'] = now();

        $user->fill($data)->save();

        return $user;
    }

    public function uploadAvatar()
    {
        $user = $this->model->find(auth()->user()->id);

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
        $user = $this->model->find(auth()->user()->id);

        if (!$user) {
            return response()->json(['message' => "Not found"], 404);
        }

        $hashedPassword = auth()->user()->getAuthPassword();
        if (!Hash::check($data['old_password'], $hashedPassword)) {
            return response()->json(['message' => "Password is not correct"], 400);
        }

        $user->fill(['password' => Hash::make($data['new_password'])])->save();
        return $user;
    }
}
