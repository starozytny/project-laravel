<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Show the list of users.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $role = UserRole::tryFrom($request->string('role')->toString());

        $users = User::query()
            ->when($search !== '', fn ($query) => $query->where(
                fn ($query) => $query
                    ->whereLike('name', "%{$search}%")
                    ->orWhereLike('email', "%{$search}%")
            ))
            ->when($role, fn ($query) => $query->role($role->value))
            ->with('roles')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/users/index', [
            'users' => UserResource::collection($users),
            'filters' => [
                'search' => $search,
                'role' => $role->value ?? '',
            ],
            'roleOptions' => collect(UserRole::cases())->map(fn (UserRole $role): array => [
                'value' => $role->value,
                'label' => $role->label(),
            ])->all(),
        ]);
    }
}
