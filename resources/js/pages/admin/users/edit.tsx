import { Form, Head } from '@inertiajs/react';

import UserForm from '@/components/admin/user/user-form';

import { AdminUser } from '@/types/admin/user';

import { update, index } from '@/routes/admin/users';

type Props = {
    user: AdminUser;
    isSelf: boolean;
    roleOptions: { value: string; label: string }[];
};

export default function UsersEdit({ user, isSelf, roleOptions }: Props) {
    return (
        <>
            <Head title="Modifier un utilisateur" />
            <div className="flex flex-col gap-4 p-4">
                <h1 className="text-xl font-semibold">
                    Modifier un utilisateur
                </h1>

                <Form
                    {...update.form(user.id)}
                    className="flex max-w-md flex-col gap-6"
                >
                    {({ processing, errors }) => (
                        <UserForm
                            roleOptions={roleOptions}
                            processing={processing}
                            errors={errors}
                            user={user}
                            isSelf={isSelf}
                        />
                    )}
                </Form>
            </div>
        </>
    );
}

UsersEdit.layout = {
    breadcrumbs: [
        { title: 'Utilisateurs', href: index() },
        { title: 'Modifier', href: index() },
    ],
};
