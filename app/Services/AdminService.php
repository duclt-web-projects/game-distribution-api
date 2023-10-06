<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Http\Request;
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
}
