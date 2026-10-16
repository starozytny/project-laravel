<?php

namespace App\Actions\User;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class CreateUserAction
{
    use PasswordValidationRules;
    use ProfileValidationRules;

    /**
     * @param  array{name: string, email: string, password: string, password_confirmation: string}  $input
     */
    public function handle(array $input): User
    {
        Validator::make($input, [
            'name' => $this->nameRules(),
            'email' => $this->emailRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);
    }
}
