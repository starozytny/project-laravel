import { Head, Link } from '@inertiajs/react';

import { Button } from '@/components/ui/button';

import { home } from '@/routes';

type Props = {
    status: number;
};

const messages: Record<number, { title: string; description: string }> = {
    403: {
        title: 'Accès refusé',
        description: "Vous n'avez pas l'autorisation d'accéder à cette page.",
    },
    404: {
        title: 'Page introuvable',
        description: "La page demandée n'existe pas ou a été déplacée.",
    },
    500: {
        title: 'Erreur du serveur',
        description: 'Une erreur est survenue. Réessayez dans un instant.',
    },
    503: {
        title: 'Service indisponible',
        description: 'Le service est en maintenance. Revenez bientôt.',
    },
};

const fallbackMessage = {
    title: 'Une erreur est survenue',
    description: "Cette page n'a pas pu être affichée.",
};

export default function Error({ status }: Props) {
    const { title, description } = messages[status] ?? fallbackMessage;

    return (
        <>
            <Head title={title} />

            <div className="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 text-center">
                <p className="text-6xl font-semibold text-muted-foreground">
                    {status}
                </p>

                <div className="space-y-2">
                    <h1 className="text-xl font-medium">{title}</h1>
                    <p className="text-sm text-muted-foreground">
                        {description}
                    </p>
                </div>

                <div className="flex gap-2">
                    <Button
                        variant="outline"
                        onClick={() => window.history.back()}
                    >
                        Retour
                    </Button>
                    <Button asChild>
                        <Link href={home()}>Accueil</Link>
                    </Button>
                </div>
            </div>
        </>
    );
}
