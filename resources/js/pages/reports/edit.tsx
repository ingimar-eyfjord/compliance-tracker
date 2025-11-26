import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface ReportEditProps {
    report: {
        id: string;
        status: string;
    };
}

export default function ReportsEdit({ report }: ReportEditProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Reports', href: '/reports' },
                { title: report.id, href: `/reports/${report.id}` },
                { title: 'Edit', href: `/reports/${report.id}/edit` },
            ]}
        >
            <Head title={`Edit Report · ${report.id}`} />
            <div className="p-6 text-lg">
                Update report <strong>{report.id}</strong> (status {report.status}).
            </div>
        </AppLayout>
    );
}
