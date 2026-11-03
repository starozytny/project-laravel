import { Head, Link } from '@inertiajs/react';

import ConfirmDeleteDialog from '@/components/admin/confirm-delete-dialog';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

import { formatDateTime } from '@/lib/format-date';

import type { AdminContactMessage } from '@/types/admin/contactMessage';

import { destroy, index } from '@/routes/admin/contact-messages';

type Props = {
    contactMessage: AdminContactMessage;
};

export default function ContactMessagesShow({ contactMessage }: Props) {
    const fullName = `${contactMessage.firstName} ${contactMessage.lastName}`;

    return (
        <>
            <Head title={`Message de ${fullName}`} />

            <div className="p-4">
                <Card className="max-w-3xl">
                    <CardHeader>
                        <CardTitle className="text-xl">{fullName}</CardTitle>
                        <CardDescription>
                            Reçu le {formatDateTime(contactMessage.createdAt)}
                        </CardDescription>
                    </CardHeader>

                    <CardContent className="space-y-6">
                        <dl className="grid gap-4 sm:grid-cols-3">
                            <div>
                                <dt className="text-sm text-muted-foreground">
                                    Société
                                </dt>
                                <dd>{contactMessage.company ?? '—'}</dd>
                            </div>
                            <div>
                                <dt className="text-sm text-muted-foreground">
                                    Téléphone
                                </dt>
                                <dd>
                                    <a
                                        href={`tel:${contactMessage.phone}`}
                                        className="underline underline-offset-4"
                                    >
                                        {contactMessage.phone}
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt className="text-sm text-muted-foreground">
                                    Adresse e-mail
                                </dt>
                                <dd>
                                    {contactMessage.email ? (
                                        <a
                                            href={`mailto:${contactMessage.email}`}
                                            className="break-all underline underline-offset-4"
                                        >
                                            {contactMessage.email}
                                        </a>
                                    ) : (
                                        '—'
                                    )}
                                </dd>
                            </div>
                        </dl>

                        <div className="space-y-2">
                            <h2 className="text-sm text-muted-foreground">
                                Message
                            </h2>
                            <p className="rounded-md border bg-muted/40 p-4 break-words whitespace-pre-wrap">
                                {contactMessage.message}
                            </p>
                        </div>
                    </CardContent>

                    <CardFooter className="flex flex-wrap items-center justify-between gap-2">
                        <Button variant="outline" asChild>
                            <Link href={index()}>Retour à la liste</Link>
                        </Button>

                        <div className="flex items-center gap-2">
                            {contactMessage.email && (
                                <Button asChild>
                                    <a href={`mailto:${contactMessage.email}`}>
                                        Répondre par e-mail
                                    </a>
                                </Button>
                            )}
                            <ConfirmDeleteDialog
                                form={destroy.form(contactMessage.id)}
                                label="Supprimer"
                                ariaLabel={`Supprimer le message de ${fullName}`}
                                title="Voulez-vous vraiment supprimer ce message ?"
                                description="Cette action est irréversible."
                            />
                        </div>
                    </CardFooter>
                </Card>
            </div>
        </>
    );
}

ContactMessagesShow.layout = {
    breadcrumbs: [
        { title: 'Messages', href: index() },
        { title: 'Message', href: index() },
    ],
};
