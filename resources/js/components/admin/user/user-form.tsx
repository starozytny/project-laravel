import { Link } from '@inertiajs/react';

import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { Checkbox } from '@/components/ui/checkbox';

import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';

import { AdminUser } from '@/types/admin/user';

import { index } from '@/routes/admin/users';

type Props = {
    roleOptions: { value: string; label: string }[];
    processing: boolean;
    errors: Partial<Record<string, string>>;
    user?: AdminUser;
    isSelf?: boolean;
};

export default function UserForm({
    roleOptions,
    processing,
    errors,
    user,
    isSelf = false,
}: Props) {
    return (
        <>
            <div className="grid gap-2">
                <Label htmlFor="name">Nom</Label>
                <Input
                    id="name"
                    type="text"
                    name="name"
                    defaultValue={user?.name}
                    required
                    autoFocus
                />
                <InputError message={errors.name} />
            </div>

            <div className="grid gap-2">
                <Label htmlFor="email">Adresse e-mail</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    defaultValue={user?.email}
                    required
                    placeholder="email@exemple.com"
                />
                <InputError message={errors.email} />
            </div>

            {!user && (
                <div className="grid gap-2">
                    <Label htmlFor="password">Mot de passe</Label>
                    <PasswordInput
                        id="password"
                        name="password"
                        required
                        placeholder="Mot de passe"
                        autoComplete="new-password"
                    />
                    <InputError message={errors.password} />
                </div>
            )}

            <div className="grid gap-2">
                <Label>Rôles</Label>
                {isSelf ? (
                    <div className="text-sm text-muted-foreground">
                        Vous ne pouvez pas modifier vos propres rôles
                    </div>
                ) : (
                    roleOptions.map((option) => (
                        <div
                            key={option.value}
                            className="flex items-center gap-2"
                        >
                            <Checkbox
                                id={`role-${option.value}`}
                                name="roles[]"
                                value={option.value}
                                defaultChecked={
                                    user
                                        ? user.roles.some(
                                              (role) =>
                                                  role.value === option.value,
                                          )
                                        : option.value === 'user'
                                }
                            />
                            <Label htmlFor={`role-${option.value}`}>
                                {option.label}
                            </Label>
                        </div>
                    ))
                )}
                <InputError message={errors.roles} />
            </div>

            <div className="mt-4 flex gap-2">
                <Button variant="outline" asChild>
                    <Link href={index()}>Annuler</Link>
                </Button>
                <Button
                    type="submit"
                    disabled={processing}
                    data-test="submit-user-button"
                >
                    {processing && <Spinner />}
                    {user ? 'Enregistrer' : "Créer l'utilisateur"}
                </Button>
            </div>
        </>
    );
}
