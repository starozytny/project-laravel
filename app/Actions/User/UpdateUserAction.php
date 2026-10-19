<?php

namespace App\Actions\User;

use App\Concerns\ProfileValidationRules;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateUserAction
{
    use ProfileValidationRules;

    /**
     * @param  array<string, mixed>  $input
     */
    public function handle(User $user, array $input): User
    {
        Validator::make($input, [
            'name' => $this->nameRules(),
            'email' => $this->emailRules($user->id),
            'roles' => ['sometimes', 'array', 'min:1'],
            'roles.*' => [Rule::enum(UserRole::class)],
        ])->validate();

        return DB::transaction(function () use ($user, $input): User {
            $user->update([
                'name' => $input['name'],
                'email' => $input['email'],
            ]);

            if (isset($input['roles'])) {
                $user->syncRoles($input['roles']);
            }

            return $user;
        });
    }
}
