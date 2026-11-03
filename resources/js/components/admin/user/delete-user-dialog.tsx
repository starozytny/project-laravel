import ConfirmDeleteDialog from '@/components/admin/confirm-delete-dialog';

import { AdminUser } from '@/types/admin/user';

import { destroy } from '@/routes/admin/users';

type Props = {
    user: AdminUser;
};

export default function DeleteUserDialog({ user }: Props) {
    return (
        <ConfirmDeleteDialog
            form={destroy.form(user.id)}
            label="Supprimer"
            ariaLabel={`Supprimer ${user.name}`}
            title={`Voulez-vous vraiment supprimer ${user.name} ?`}
            description="Cette action est irréversible."
        />
    );
}
