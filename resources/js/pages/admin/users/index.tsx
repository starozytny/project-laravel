import { useEffect, useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { Search } from 'lucide-react';

import { Input } from '@/components/ui/input';
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
    filters: { search: string };
};

export default function UsersIndex({ users, filters }: Props) {
    const { data, links, meta } = users;
    const [search, setSearch] = useState(filters.search);

    useEffect(() => {
        if (search === filters.search) return;

        const timeout = setTimeout(() => {
            router.get(
                index().url,
                { search: search || undefined },
                { preserveState: true, preserveScroll: true, replace: true },
            );
        }, 300);

        return () => clearTimeout(timeout);
    }, [search]);

    return (
        <>
            <Head title="Utilisateurs" />

            <div className="flex flex-col gap-4 p-4">
                <h1 className="text-xl font-semibold">Utilisateurs</h1>

                <div className="relative max-w-sm">
                    <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        type="search"
                        value={search}
                        onChange={(event) => setSearch(event.target.value)}
                        placeholder="Rechercher par nom ou e-mail…"
                        className="pl-9"
                    />
                </div>

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
                                        Aucun utilisateur
                                        {filters.search
                                            ? ` ne correspond à « ${filters.search} ».`
                                            : '.'}
                                    </TableCell>
                                </TableRow>
                            ) : (
                                data.map((user) => (
                                    <TableRow key={user.id}>
                                        <TableCell className="font-medium">
                                            {user.name}
                                        </TableCell>
                                        <TableCell>{user.email}</TableCell>
                                        <TableCell>
                                            <div className="flex flex-wrap gap-1">
                                                {user.roles.map((role) => (
                                                    <Badge
                                                        key={role.value}
                                                        variant="secondary"
                                                    >
                                                        {role.label}
                                                    </Badge>
                                                ))}
                                            </div>
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
                        {links.prev ? (
                            <Button variant="outline" size="sm" asChild>
                                <Link href={links.prev} preserveScroll>
                                    Précédent
                                </Link>
                            </Button>
                        ) : (
                            <Button variant="outline" size="sm" disabled>
                                Précédent
                            </Button>
                        )}
                        {links.next ? (
                            <Button variant="outline" size="sm" asChild>
                                <Link href={links.next} preserveScroll>
                                    Suivant
                                </Link>
                            </Button>
                        ) : (
                            <Button variant="outline" size="sm" disabled>
                                Suivant
                            </Button>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}

UsersIndex.layout = {
    breadcrumbs: [{ title: 'Utilisateurs', href: index() }],
};
