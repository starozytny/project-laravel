import { Link, usePage } from '@inertiajs/react';

import AppLogo from '@/components/app-logo';
import { PublicFooter } from '@/components/public/public-footer';
import { Button } from '@/components/ui/button';

import { home, login } from '@/routes';
import { create as contactCreate } from '@/routes/public/contact';
import { dashboard as dashboardAdmin } from '@/routes/admin';
import { dashboard as dashboardUser } from '@/routes/user';

export default function PublicLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    const { auth } = usePage().props;

    const dashboardHref = auth.abilities.accessAdmin
        ? dashboardAdmin()
        : dashboardUser();

    return (
        <div className="flex min-h-svh flex-col bg-background text-foreground">
            <header className="sticky top-0 z-40 border-b bg-background/80 backdrop-blur">
                <div className="mx-auto flex h-16 w-full max-w-6xl items-center justify-between px-6">
                    <Link href={home()} className="flex items-center gap-2">
                        <AppLogo />
                    </Link>

                    <nav
                        aria-label="Navigation principale"
                        className="flex gap-2"
                    >
                        <Button variant="ghost" asChild>
                            <Link href={contactCreate()}>Contact</Link>
                        </Button>
                        {auth.user ? (
                            <Button asChild>
                                <Link href={dashboardHref}>Mon espace</Link>
                            </Button>
                        ) : (
                            <Button variant="outline" asChild>
                                <Link href={login()}>Se connecter</Link>
                            </Button>
                        )}
                    </nav>
                </div>
            </header>

            <main className="flex-1">{children}</main>

            <PublicFooter />
        </div>
    );
}
