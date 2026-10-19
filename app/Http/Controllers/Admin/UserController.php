<?php

namespace App\Http\Controllers\Admin;

use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
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
     * Show the form to create a user
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
        $createUserAction->handle($request->only(['name', 'email', 'password', 'roles']));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Utilisateur créé.']);

        return to_route('admin.users.index');
    }

    /**
     * Show the form to edit a user
     */
    public function edit(Request $request, User $user): Response
    {
        return Inertia::render('admin/users/edit', [
            'user' => (new UserResource($user->load('roles')))->resolve(),
            'isSelf' => $user->is($request->user()),
            'roleOptions' => UserRole::options(),
        ]);
    }

    /**
     * Update a user
     */
    public function update(Request $request, User $user, UpdateUserAction $updateUserAction): RedirectResponse
    {
        $input = $request->only(['name', 'email', 'roles']);

        if ($user->is($request->user())) {
            unset($input['roles']);
        }

        $updateUserAction->handle($user, $input);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Utilisateur modifié.']);

        return to_route('admin.users.index');
    }
}
