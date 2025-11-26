import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface BreachSummary {
    id: string;
    description: string;
    detected_at: string;
    authority_notified: boolean;
}

interface BreachIndexProps {
    breaches: BreachSummary[];
}

export default function BreachIndex({ breaches }: BreachIndexProps) {
    const first = breaches[0] ?? null;

    return (
        <AppLayout breadcrumbs={[{ title: 'Breaches', href: '/breaches' }]}
        >
            <Head title="Breach Log" />
            <div className="p-6 text-lg">
                {first ? (
                    <div>
                        First breach: <strong>{first.description}</strong>
                    </div>
                ) : (
                    <div>No breach records yet.</div>
                )}
            </div>
        </AppLayout>
    );
}
