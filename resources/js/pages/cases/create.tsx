import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface CasesCreateProps {
    reports: { id: string; reference_code: string }[];
    statuses: string[];
    priorities: string[];
}

export default function CasesCreate({ reports }: CasesCreateProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Cases', href: '/cases' },
                { title: 'Create', href: '/cases/create' },
            ]}
        >
            <Head title="Create Case" />
            <div className="p-6 text-lg">
                Case creation form placeholder. Select a report ({reports.length} options available).
            </div>
        </AppLayout>
    );
}
