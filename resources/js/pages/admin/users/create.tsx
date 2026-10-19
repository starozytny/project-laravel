import { Form, Head } from '@inertiajs/react';

import { store, create, index } from '@/routes/admin/users';
import UserForm from '@/components/admin/user/user-form';

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
                        <UserForm roleOptions={roleOptions} processing={processing} errors={errors} />
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
