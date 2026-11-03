import { Form } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';

import IconButton from '@/components/icon-button';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

import type { RouteFormDefinition } from '@/wayfinder';

type Props = {
    form: RouteFormDefinition<'post'>;
    label: string;
    ariaLabel?: string;
    title: string;
    description: string;
};

export default function ConfirmDeleteDialog({
    form,
    label,
    ariaLabel,
    title,
    description,
}: Props) {
    return (
        <Dialog>
            <DialogTrigger asChild>
                <IconButton icon={Trash2} label={label} ariaLabel={ariaLabel} />
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>{title}</DialogTitle>
                <DialogDescription>{description}</DialogDescription>

                <Form
                    {...form}
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
                                data-test="confirm-delete-button"
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
