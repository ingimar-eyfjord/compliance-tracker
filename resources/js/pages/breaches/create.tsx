import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface BreachCreateProps {
    cases: { id: string }[];
}

export default function BreachCreate({ cases }: BreachCreateProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Breaches', href: '/breaches' },
                { title: 'Create', href: '/breaches/create' },
            ]}
        >
            <Head title="Record Breach" />
            <div className="p-6 text-lg">
                Breach form placeholder. Linked cases available: {cases.length}.
            </div>
        </AppLayout>
    );
}
