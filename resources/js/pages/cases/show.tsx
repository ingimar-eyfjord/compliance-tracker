import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface CaseShowProps {
    caseItem: {
        id: string;
        status: string;
        priority: string;
        assignee?: string;
        reference?: string;
    };
}

export default function CaseShow({ caseItem: caseFile }: CaseShowProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Cases', href: '/cases' },
                { title: caseFile.reference ?? caseFile.id, href: `/cases/${caseFile.id}` },
            ]}
        >
            <Head title={`Case · ${caseFile.reference ?? caseFile.id}`} />
            <div className="p-6 text-lg">
                Case <strong>{caseFile.reference ?? caseFile.id}</strong> is currently {caseFile.status}.
            </div>
        </AppLayout>
    );
}
