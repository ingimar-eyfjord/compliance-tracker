import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface ReportSummary {
    id: string;
    reference_code: string;
    channel?: string;
    created_via: string;
}

interface ReportsIndexProps {
    reports: ReportSummary[];
}

export default function ReportsIndex({ reports }: ReportsIndexProps) {
    const first = reports[0] ?? null;

    return (
        <AppLayout breadcrumbs={[{ title: 'Reports', href: '/reports' }]}
        >
            <Head title="Reports" />
            <div className="p-6 text-lg">
                {first ? (
                    <div>
                        First report: <strong>{first.reference_code}</strong> via {first.created_via}
                    </div>
                ) : (
                    <div>No reports yet.</div>
                )}
            </div>
        </AppLayout>
    );
}
