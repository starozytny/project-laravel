import { Head } from '@inertiajs/react';
import { Eye } from 'lucide-react';

import ConfirmDeleteDialog from '@/components/admin/confirm-delete-dialog';
import PaginationLinks from '@/components/admin/pagination-links';
import IconLinkButton from '@/components/icon-link-button';
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

import { formatDateTime } from '@/lib/format-date';

import type { AdminContactMessage } from '@/types/admin/contactMessage';
import type { Paginated } from '@/types/pagination';

import { destroy, index, show } from '@/routes/admin/contact-messages';

type Props = {
    contactMessages: Paginated<AdminContactMessage>;
};

export default function ContactMessagesIndex({ contactMessages }: Props) {
    const { data, links, meta } = contactMessages;

    return (
        <>
            <Head title="Messages" />

            <div className="flex flex-col gap-4 p-4">
                <h1 className="text-xl font-semibold">Messages</h1>

                <div className="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Expéditeur</TableHead>
                                <TableHead>Coordonnées</TableHead>
                                <TableHead className="hidden md:table-cell">
                                    Message
                                </TableHead>
                                <TableHead>Reçu le</TableHead>
                                <TableHead className="w-24">
                                    <span className="sr-only">Actions</span>
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={5}
                                        className="h-24 text-center text-muted-foreground"
                                    >
                                        Aucun message.
                                    </TableCell>
                                </TableRow>
                            ) : (
                                data.map((contactMessage) => (
                                    <TableRow key={contactMessage.id}>
                                        <TableCell>
                                            <div className="flex items-center gap-2">
                                                <span
                                                    className={
                                                        contactMessage.isRead
                                                            ? 'font-medium'
                                                            : 'font-semibold'
                                                    }
                                                >
                                                    {contactMessage.firstName}{' '}
                                                    {contactMessage.lastName}
                                                </span>
                                                {!contactMessage.isRead && (
                                                    <Badge>Nouveau</Badge>
                                                )}
                                            </div>
                                            {contactMessage.company && (
                                                <p className="text-sm text-muted-foreground">
                                                    {contactMessage.company}
                                                </p>
                                            )}
                                        </TableCell>
                                        <TableCell>
                                            <p>{contactMessage.phone}</p>
                                            {contactMessage.email && (
                                                <p className="text-sm text-muted-foreground">
                                                    {contactMessage.email}
                                                </p>
                                            )}
                                        </TableCell>
                                        <TableCell className="hidden max-w-xs md:table-cell">
                                            <p className="truncate text-muted-foreground">
                                                {contactMessage.message}
                                            </p>
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-muted-foreground">
                                            {formatDateTime(
                                                contactMessage.createdAt,
                                            )}
                                        </TableCell>
                                        <TableCell>
                                            <div className="flex justify-end">
                                                <IconLinkButton
                                                    href={show(
                                                        contactMessage.id,
                                                    )}
                                                    icon={Eye}
                                                    label="Voir"
                                                    ariaLabel={`Voir le message de ${contactMessage.firstName} ${contactMessage.lastName}`}
                                                />
                                                <ConfirmDeleteDialog
                                                    form={destroy.form(
                                                        contactMessage.id,
                                                    )}
                                                    label="Supprimer"
                                                    ariaLabel={`Supprimer le message de ${contactMessage.firstName} ${contactMessage.lastName}`}
                                                    title="Voulez-vous vraiment supprimer ce message ?"
                                                    description="Cette action est irréversible."
                                                />
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </div>

                <PaginationLinks links={links} meta={meta} />
            </div>
        </>
    );
}

ContactMessagesIndex.layout = {
    breadcrumbs: [{ title: 'Messages', href: index() }],
};
