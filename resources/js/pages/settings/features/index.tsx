import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface FeatureEntry {
    feature_key: string;
    enabled: boolean;
    source: string;
    enabled_at?: string | null;
    expires_at?: string | null;
}

interface FeaturesProps {
    features: Record<string, FeatureEntry[]>;
}

export default function FeaturesIndex({ features }: FeaturesProps) {
    const keys = Object.keys(features);
    const firstKey = keys[0] ?? null;
    const first = firstKey ? features[firstKey][0] : null;

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Settings', href: '/settings' },
                { title: 'Features', href: '/settings/features' },
            ]}
        >
            <Head title="Feature Toggles" />
            <div className="p-6 text-lg">
                {firstKey && first ? (
                    <div>
                        First feature: <strong>{firstKey}</strong> ({first.enabled ? 'enabled' : 'disabled'})
                    </div>
                ) : (
                    <div>No feature toggles configured.</div>
                )}
            </div>
        </AppLayout>
    );
}
