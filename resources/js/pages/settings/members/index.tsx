import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface Member {
    id: string;
    user: {
        name?: string;
        email?: string;
    };
    role: string;
    status: string;
}

interface MembersIndexProps {
    members: Member[];
}

export default function MembersIndex({ members }: MembersIndexProps) {
    const first = members[0] ?? null;

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Settings', href: '/settings' },
                { title: 'Members', href: '/settings/members' },
            ]}
        >
            <Head title="Members" />
            <div className="p-6 text-lg">
                {first ? (
                    <div>
                        First member: <strong>{first.user.name ?? 'Unnamed'}</strong> ({first.role})
                    </div>
                ) : (
                    <div>No members found.</div>
                )}
            </div>
        </AppLayout>
    );
}
