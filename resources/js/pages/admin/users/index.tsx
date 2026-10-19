import { useEffect, useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Plus, Search } from 'lucide-react';

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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

import { Paginated } from '@/types/pagination';
import { AdminUser } from '@/types/admin/user';

import { create, edit, index } from '@/routes/admin/users';

type Props = {
    users: Paginated<AdminUser>;
    filters: {
        search: string;
        role: string;
    };
    roleOptions: {
        value: string;
        label: string;
    }[];
};

function applyFilters(search: string, role: string) {
    router.get(
        index().url,
        {
            search: search || undefined,
            role: role || undefined,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

export default function UsersIndex({ users, filters, roleOptions }: Props) {
    const { data, links, meta } = users;
    const [search, setSearch] = useState(filters.search);

    useEffect(() => {
        if (search === filters.search) return;

        const timeout = setTimeout(() => {
            applyFilters(search, filters.role);
        }, 300);

        return () => clearTimeout(timeout);
    }, [search]);

    const hasFilters = filters.search !== '' || filters.role !== '';

    return (
        <>
            <Head title="Utilisateurs" />

            <div className="flex flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-xl font-semibold">Utilisateurs</h1>
                    <Button asChild>
                        <Link href={create()}>
                            <Plus />
                            Créer un utilisateur
                        </Link>
                    </Button>
                </div>

                <div className="flex flex-wrap gap-2">
                    <div className="relative w-full max-w-sm">
                        <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            type="search"
                            value={search}
                            onChange={(event) => setSearch(event.target.value)}
                            placeholder="Rechercher par nom ou e-mail…"
                            className="pl-9"
                        />
                    </div>

                    <Select
                        value={filters.role || 'all'}
                        onValueChange={(value) =>
                            applyFilters(search, value === 'all' ? '' : value)
                        }
                    >
                        <SelectTrigger className="w-48">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Tous les rôles</SelectItem>
                            {roleOptions.map((option) => (
                                <SelectItem
                                    key={option.value}
                                    value={option.value}
                                >
                                    {option.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                <div className="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nom</TableHead>
                                <TableHead>Adresse e-mail</TableHead>
                                <TableHead>Rôles</TableHead>
                                <TableHead className="w-12" />
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={4}
                                        className="h-24 text-center text-muted-foreground"
                                    >
                                        {hasFilters
                                            ? 'Aucun utilisateur ne correspond aux filtres.'
                                            : 'Aucun utilisateur.'}
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
                                        <TableCell>
                                            <Button variant="ghost" size="icon" asChild>
                                                <Link href={edit(user.id)} aria-label={`Modifier ${user.name}`}>
                                                    <Pencil />
                                                </Link>
                                            </Button>
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
