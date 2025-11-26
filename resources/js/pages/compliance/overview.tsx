import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface ComplianceOverviewProps {
    summary: {
        open_cases: number;
        breaches: number;
    };
    deadlines: {
        type: string;
        status: string;
        total: number;
    }[];
}

export default function ComplianceOverview({ summary, deadlines }: ComplianceOverviewProps) {
    const first = deadlines[0] ?? null;

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Compliance', href: '/compliance/overview' },
            ]}
        >
            <Head title="Compliance Overview" />
            <div className="p-6 text-lg space-y-4">
                <div>
                    Open cases: <strong>{summary.open_cases}</strong> · Breaches: <strong>{summary.breaches}</strong>
                </div>
                <div>
                    {first ? (
                        <>First deadline snapshot: {first.type} — {first.status} ({first.total})</>
                    ) : (
                        <>No deadline data yet.</>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
