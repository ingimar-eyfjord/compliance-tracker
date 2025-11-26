import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface ReportsCreateProps {
    channels: { id: string; name: string }[];
}

export default function ReportsCreate({ channels }: ReportsCreateProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Reports', href: '/reports' },
                { title: 'Create', href: '/reports/create' },
            ]}
        >
            <Head title="Create Report" />
            <div className="p-6 text-lg">
                Report submission placeholder. Channels available: {channels.length}.
            </div>
        </AppLayout>
    );
}
