import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface ChannelShowProps {
    channel: {
        id: string;
        name: string;
        slug: string;
        status: string;
    };
}

export default function ChannelShow({ channel }: ChannelShowProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Channels', href: '/channels' },
                { title: channel.name, href: `/channels/${channel.id}` },
            ]}
        >
            <Head title={`Channel · ${channel.name}`} />
            <div className="p-6 text-lg">
                Channel <strong>{channel.name}</strong> currently has status {channel.status}.
            </div>
        </AppLayout>
    );
}
