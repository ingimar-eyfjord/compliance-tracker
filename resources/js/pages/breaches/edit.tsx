import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface BreachEditProps {
    breach: {
        id: string;
        description: string;
        authority_notified: boolean;
    };
}

export default function BreachEdit({ breach }: BreachEditProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Breaches', href: '/breaches' },
                { title: breach.id, href: `/breaches/${breach.id}` },
                { title: 'Edit', href: `/breaches/${breach.id}/edit` },
            ]}
        >
            <Head title={`Edit Breach · ${breach.id}`} />
            <div className="p-6 text-lg">
                Editing breach <strong>{breach.id}</strong>.
            </div>
        </AppLayout>
    );
}
