import { Form, Head, Link } from '@inertiajs/react';

import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { Checkbox } from '@/components/ui/checkbox';

import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';

import { store, create, index } from '@/routes/admin/users';

type Props = {
    roleOptions: { value: string; label: string }[];
};

export default function UsersCreate({ roleOptions }: Props) {
    return (
        <>
            <Head title="Créer un utilisateur" />
            <div className="flex flex-col gap-4 p-4">
                <h1 className="text-xl font-semibold">Créer un utilisateur</h1>

                <Form
                    {...store.form()}
                    resetOnSuccess={['password']}
                    className="flex max-w-md flex-col gap-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="name">Nom</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    name="name"
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
                                    required
                                    placeholder="email@exemple.com"
                                />
                                <InputError message={errors.email} />
                            </div>

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

                            <div className="grid gap-2">
                                <Label>Rôles</Label>
                                {roleOptions.map((option) => (
                                    <div
                                        key={option.value}
                                        className="flex items-center gap-2"
                                    >
                                        <Checkbox
                                            id={`role-${option.value}`}
                                            name="roles[]"
                                            value={option.value}
                                            defaultChecked={
                                                option.value === 'user'
                                            }
                                        />
                                        <Label htmlFor={`role-${option.value}`}>
                                            {option.label}
                                        </Label>
                                    </div>
                                ))}
                                <InputError message={errors.roles} />
                            </div>

                            <div className="mt-4 flex gap-2">
                                <Button variant="outline" asChild>
                                    <Link href={index()}>Annuler</Link>
                                </Button>
                                <Button
                                    type="submit"
                                    disabled={processing}
                                    data-test="create-user-button"
                                >
                                    {processing && <Spinner />}
                                    Créer l'utilisateur
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

UsersCreate.layout = {
    breadcrumbs: [
        { title: 'Utilisateurs', href: index() },
        { title: 'Créer', href: create() },
    ],
};
