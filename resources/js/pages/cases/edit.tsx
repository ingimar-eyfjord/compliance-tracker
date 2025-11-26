import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface CaseEditProps {
    caseItem: {
        id: string;
        status: string;
        priority: string;
        assignee_user_id?: string | null;
        due_at?: string | null;
    };
    statuses: string[];
    priorities: string[];
}

export default function CaseEdit({ caseItem: caseFile }: CaseEditProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Cases', href: '/cases' },
                { title: caseFile.id, href: `/cases/${caseFile.id}` },
                { title: 'Edit', href: `/cases/${caseFile.id}/edit` },
            ]}
        >
            <Head title={`Edit Case · ${caseFile.id}`} />
            <div className="p-6 text-lg">
                Update case <strong>{caseFile.id}</strong> (current status {caseFile.status}).
            </div>
        </AppLayout>
    );
}
