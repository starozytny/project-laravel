<?php

namespace App\Console\Commands;

use App\Actions\User\CreateUserAction;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('user:create {--name=} {--email=}')]
#[Description('Créer un utilisateur')]
class UserCreateCommand extends Command
{
    public function handle(CreateUserAction $createUserAction): int
    {
        $email = $this->askIfMissing('email', 'Adresse e-mail');

        if (User::where('email', $email)->exists()) {
            $this->error('Cette adresse e-mail est déjà utilisée.');

            return self::FAILURE;
        }

        $name = $this->askIfMissing('name', 'Nom');
        $password = password(label: 'Mot de passe', required: true);
        $passwordConfirmation = password(label: 'Confirmer le mot de passe', required: true);

        try {
            $createUserAction->handle([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
            ]);
        } catch (ValidationException $e) {
            foreach (Arr::flatten($e->errors()) as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $this->info("L'utilisateur {$name} a été créé avec succès.");

        return self::SUCCESS;
    }

    private function askIfMissing(string $option, string $label): string
    {
        $value = $this->option($option);

        if (is_string($value) && $value !== '') {
            return $value;
        }

        return text(label: $label, required: true);
    }
}
