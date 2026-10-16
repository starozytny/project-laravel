<?php

namespace App\Actions\User;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateUserAction
{
    use PasswordValidationRules;
    use ProfileValidationRules;

    /**
     * @param  array{name: string, email: string, password: string, password_confirmation: string, roles?: array<int, string>}  $input
     */
    public function handle(array $input): User
    {
        Validator::make($input, [
            'name' => $this->nameRules(),
            'email' => $this->emailRules(),
            'password' => $this->passwordRules(),
            'roles' => ['sometimes', 'array', 'min:1'],
            'roles.*' => [Rule::enum(UserRole::class)],
        ])->validate();

        return DB::transaction(function () use ($input): User {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
            ]);

            $user->assignRole($input['roles'] ?? [UserRole::User->value]);

            return $user;
        });
    }
}
