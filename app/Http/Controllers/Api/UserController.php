<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Search users by name and filter by tenant.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $perPage = 10;

        $query = User::query();

        // Filter by tenant - only show users from the same tenant using the session value
        $query->where('tenant_id', session('tenant_id'));

        // Search by name
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('position', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->paginate($perPage, ['id', 'name', 'email', 'position']);

        return response()->json($users);
    }
}
