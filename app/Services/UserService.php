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
}
