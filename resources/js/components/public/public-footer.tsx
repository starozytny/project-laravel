import { Link, usePage } from '@inertiajs/react';

import { notice, privacy } from '@/routes/legal';

const footerLinks = [
    { title: 'Mentions légales', href: notice() },
    { title: 'Politique de confidentialité', href: privacy() },
];

export function PublicFooter() {
    const { name } = usePage().props;

    return (
        <footer className="border-t">
            <div className="mx-auto flex w-full max-w-6xl flex-col gap-4 px-6 py-8 text-sm text-muted-foreground sm:flex-row sm:items-center sm:justify-between">
                <p>© 2026 {name}. Tous droits réservés.</p>

                <nav aria-label="Informations légales">
                    <ul className="flex flex-wrap gap-x-6 gap-y-2">
                        {footerLinks.map((link) => (
                            <li key={link.title}>
                                <Link
                                    href={link.href}
                                    className="transition-colors hover:text-foreground"
                                >
                                    {link.title}
                                </Link>
                            </li>
                        ))}
                    </ul>
                </nav>
            </div>
        </footer>
    );
}
