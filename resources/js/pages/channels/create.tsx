import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

export default function ChannelCreate() {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Channels', href: '/channels' },
                { title: 'Create', href: '/channels/create' },
            ]}
        >
            <Head title="Create Channel" />
            <div className="p-6 text-lg">Channel creation form will appear here.</div>
        </AppLayout>
    );
}
