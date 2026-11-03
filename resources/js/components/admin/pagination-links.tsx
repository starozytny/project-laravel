import { Link } from '@inertiajs/react';

import { Button } from '@/components/ui/button';

import type { Paginated } from '@/types/pagination';

type Props = Pick<Paginated<unknown>, 'links' | 'meta'>;

export default function PaginationLinks({ links, meta }: Props) {
    return (
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
    );
}
