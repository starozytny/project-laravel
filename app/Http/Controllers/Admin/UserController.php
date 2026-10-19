<?php

namespace App\Http\Controllers\Admin;

use App\Actions\User\CreateUserAction;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
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
            'roleOptions' => UserRole::options(),
        ]);
    }

    /**
     * Create a user
     */
    public function create(): Response
    {
        return Inertia::render('admin/users/create', [
            'roleOptions' => UserRole::options(),
        ]);
    }

    /**
     * Store a user
     */
    public function store(Request $request, CreateUserAction $createUserAction): RedirectResponse
    {
        $createUserAction->handle($request->only(['name', 'email', 'password', 'password_confirmation', 'roles']));

        return to_route('admin.users.index')->with('success', 'Utilisateur créé.');
    }
}
