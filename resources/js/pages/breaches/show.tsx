import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface BreachShowProps {
    breach: {
        id: string;
        description: string;
        detected_at: string;
        authority_notified: boolean;
    };
}

export default function BreachShow({ breach }: BreachShowProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Breaches', href: '/breaches' },
                { title: breach.id, href: `/breaches/${breach.id}` },
            ]}
        >
            <Head title={`Breach · ${breach.id}`} />
            <div className="p-6 text-lg">
                Breach recorded on {new Date(breach.detected_at).toLocaleString()} — {breach.description}.
            </div>
        </AppLayout>
    );
}
