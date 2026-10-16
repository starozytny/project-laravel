<?php

namespace App\Console\Commands;

use App\Actions\User\CreateUserAction;
use App\Enums\UserRole;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('user:create {--name=} {--email=} {--role=* : Rôle(s) à attribuer}')]
#[Description('Créer un utilisateur')]
class UserCreateCommand extends Command
{
    public function handle(CreateUserAction $createUserAction): int
    {
        $email = $this->askIfMissing('email', 'Adresse e-mail');
        $name = $this->askIfMissing('name', 'Nom');
        $roles = $this->askRolesIfMissing();
        $password = password(label: 'Mot de passe', required: true);
        $passwordConfirmation = password(label: 'Confirmer le mot de passe', required: true);

        try {
            $createUserAction->handle([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
                'roles' => $roles,
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

    /**
     * @return array<int, string>
     */
    private function askRolesIfMissing(): array
    {
        $roles = $this->option('role');

        if (is_array($roles) && $roles !== []) {
            return array_values(array_filter($roles, 'is_string'));
        }

        $selected = multiselect(
            label: 'Rôles',
            options: collect(UserRole::cases())
                ->mapWithKeys(fn (UserRole $role) => [$role->value => $role->label()])
                ->all(),
            default: [UserRole::User->value],
            required: true,
        );

        return array_map(fn (int|string $role): string => UserRole::from($role)->value, $selected);
    }
}
