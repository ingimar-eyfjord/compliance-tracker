import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface ChannelEditProps {
    channel: {
        id: string;
        name: string;
        slug: string;
        status: string;
    };
}

export default function ChannelEdit({ channel }: ChannelEditProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Channels', href: '/channels' },
                { title: channel.name, href: `/channels/${channel.id}` },
                { title: 'Edit', href: `/channels/${channel.id}/edit` },
            ]}
        >
            <Head title={`Edit Channel · ${channel.name}`} />
            <div className="p-6 text-lg">
                Editing <strong>{channel.name}</strong> (slug: {channel.slug}).
            </div>
        </AppLayout>
    );
}
