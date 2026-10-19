import { Head, Link } from '@inertiajs/react';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

import { Paginated } from '@/types/pagination';
import { AdminUser } from '@/types/admin/user';

import { index } from '@/routes/admin/users';

type Props = {
    users: Paginated<AdminUser>;
};

export default function UsersIndex({ users }: Props) {
    const { data, links, meta } = users;

    return (
        <>
            <Head title="Utilisateurs" />

            <div className="flex flex-col gap-4 p-4">
                <h1 className="text-xl font-semibold">Utilisateurs</h1>

                <div className="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nom</TableHead>
                                <TableHead>Adresse e-mail</TableHead>
                                <TableHead>Rôles</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={3}
                                        className="h-24 text-center text-muted-foreground"
                                    >
                                        Aucun utilisateur.
                                    </TableCell>
                                </TableRow>
                            ) : (
                                data.map((user) => (
                                    <TableRow key={user.id}>
                                        <TableCell className="font-medium">
                                            {user.name}
                                        </TableCell>
                                        <TableCell>{user.email}</TableCell>
                                        <TableCell className="flex gap-1">
                                            {user.roles.map((role) => (
                                                <Badge
                                                    key={role.value}
                                                    variant="secondary"
                                                >
                                                    {role.label}
                                                </Badge>
                                            ))}
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </div>

                <div className="flex items-center justify-between text-sm text-muted-foreground">
                    <span>
                        {meta.from ?? 0}–{meta.to ?? 0} sur {meta.total}
                    </span>
                    <div className="flex gap-2">
                        <Button
                            variant="outline"
                            size="sm"
                            asChild
                            disabled={!links.prev}
                        >
                            <Link href={links.prev ?? '#'} preserveScroll>
                                Précédent
                            </Link>
                        </Button>
                        <Button
                            variant="outline"
                            size="sm"
                            asChild
                            disabled={!links.next}
                        >
                            <Link href={links.next ?? '#'} preserveScroll>
                                Suivant
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </>
    );
}

UsersIndex.layout = {
    breadcrumbs: [{ title: 'Utilisateurs', href: index() }],
};
