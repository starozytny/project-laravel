import { Head } from '@inertiajs/react';
import type { ReactNode } from 'react';

type LegalPageProps = {
    title: string;
    children: ReactNode;
};

export function LegalPage({ title, children }: LegalPageProps) {
    return (
        <>
            <Head title={title} />

            <article className="mx-auto w-full max-w-3xl px-6 py-12 sm:py-16">
                <h1 className="mb-10 text-3xl font-semibold tracking-tight">
                    {title}
                </h1>

                <div className="space-y-10">{children}</div>
            </article>
        </>
    );
}

type LegalSectionProps = {
    title: string;
    children: ReactNode;
};

export function LegalSection({ title, children }: LegalSectionProps) {
    return (
        <section className="space-y-3">
            <h2 className="text-xl font-medium">{title}</h2>
            <div className="space-y-3 leading-7 text-muted-foreground">
                {children}
            </div>
        </section>
    );
}
