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

    public function register(Request $request)
    {
        $existUser = $this->findBy([['email', '=', $request->get("email")]]);
        if ($existUser) return null;

        $user = $this->model->create([
            'name' => $request->get("name"),
            'email' => $request->get("email"),
            'password' => Hash::make($request->get("password")),
        ]);

        return $user;
    }
}
