import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface ReportShowProps {
    report: {
        id: string;
        reference_code: string;
        channel?: string;
        created_via: string;
    };
}

export default function ReportShow({ report }: ReportShowProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Reports', href: '/reports' },
                { title: report.reference_code, href: `/reports/${report.id}` },
            ]}
        >
            <Head title={`Report · ${report.reference_code}`} />
            <div className="p-6 text-lg">
                Report <strong>{report.reference_code}</strong> submitted via {report.created_via}.
            </div>
        </AppLayout>
    );
}
