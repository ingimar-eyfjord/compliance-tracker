import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface ChannelSummary {
    id: string;
    name: string;
    slug: string;
    status: string;
}

interface ChannelsIndexProps {
    channels: ChannelSummary[];
}

export default function ChannelsIndex({ channels }: ChannelsIndexProps) {
    const first = channels[0] ?? null;

    return (
        <AppLayout
            breadcrumbs={[{ title: 'Channels', href: '/channels' }]}
        >
            <Head title="Channels" />
            <div className="p-6 text-lg">
                {first ? (
                    <div>
                        First channel: <strong>{first.name}</strong> ({first.status})
                    </div>
                ) : (
                    <div>No channels available yet.</div>
                )}
            </div>
        </AppLayout>
    );
}
