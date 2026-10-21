import { Head } from '@inertiajs/react';
import AppearanceTabs from '@/components/appearance-tabs';
import Heading from '@/components/heading';
import { edit as editAppearance } from '@/routes/appearance';

export default function Appearance() {
    return (
        <>
            <Head title="Paramètre thème" />

            <h1 className="sr-only">Paramètre thème</h1>

            <div className="space-y-6">
                <Heading
                    variant="small"
                    title="Thème"
                    description="Modifiez le theme de votre compte"
                />
                <AppearanceTabs />
            </div>
        </>
    );
}

Appearance.layout = {
    breadcrumbs: [
        {
            title: 'Paramètre thème',
            href: editAppearance(),
        },
    ],
};
