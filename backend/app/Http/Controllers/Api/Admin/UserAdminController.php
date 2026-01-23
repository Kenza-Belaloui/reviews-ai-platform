<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->select('id','name','email','role','created_at')
            ->latest()
            ->paginate(10);

        return response()->json($users);
    }
}
