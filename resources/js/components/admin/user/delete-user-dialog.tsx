import { Form } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';

import { Button } from '@/components/ui/button';
import IconButton from '@/components/icon-button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

import { AdminUser } from '@/types/admin/user';

import { destroy } from '@/routes/admin/users';

type Props = {
    user: AdminUser;
};

export default function DeleteUserDialog({ user }: Props) {
    return (
        <Dialog>
            <DialogTrigger asChild>
                <IconButton
                    icon={Trash2}
                    label="Supprimer"
                    ariaLabel={`Supprimer ${user.name}`}
                />
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>
                    Voulez-vous vraiment supprimer {user.name} ?
                </DialogTitle>
                <DialogDescription>
                    Cette action est irréversible.
                </DialogDescription>

                <Form
                    {...destroy.form(user.id)}
                    options={{
                        preserveScroll: true,
                    }}
                    className="space-y-6"
                >
                    {({ processing }) => (
                        <DialogFooter className="gap-2">
                            <DialogClose asChild>
                                <Button variant="secondary">Annuler</Button>
                            </DialogClose>
                            <Button
                                type="submit"
                                variant="destructive"
                                disabled={processing}
                                data-test="confirm-delete-user-button"
                            >
                                Supprimer
                            </Button>
                        </DialogFooter>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
