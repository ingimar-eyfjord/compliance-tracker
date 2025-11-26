import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface AuditLogEntry {
    id: string;
    event: string;
    entity_type: string;
    entity_id: string;
    created_at: string;
}

interface AuditIndexProps {
    logs: AuditLogEntry[];
}

export default function AuditIndex({ logs }: AuditIndexProps) {
    const first = logs[0] ?? null;

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Settings', href: '/settings' },
                { title: 'Audit Log', href: '/settings/audit' },
            ]}
        >
            <Head title="Audit Log" />
            <div className="p-6 text-lg">
                {first ? (
                    <div>
                        First entry: {first.event} on {first.entity_type}#{first.entity_id}
                    </div>
                ) : (
                    <div>No audit entries yet.</div>
                )}
            </div>
        </AppLayout>
    );
}
