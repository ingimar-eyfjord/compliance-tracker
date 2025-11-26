import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/auth-layout';
import { register } from '@/routes/register';
import { Form, Head } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';

type Membership = {
    organization: {
        id: string;
        name: string;
        slug: string | null;
    };
    role: string;
    role_label: string;
};

interface SelectOrganizationProps {
    memberships: Membership[];
}

export default function SelectOrganization({ memberships }: SelectOrganizationProps) {
    return (
        <AuthLayout
            title="Choose an organization"
            description="Select which workspace you want to access right now"
        >
            <Head title="Select organization" />

            <div className="flex flex-col gap-6">
                <div className="grid gap-4">
                    {memberships.length > 0 ? (
                        memberships.map((membership) => (
                            <Form
                                key={membership.organization.id}
                                action="/orgs/switch"
                                method="post"
                                className="flex items-center justify-between rounded-lg border border-muted bg-card px-4 py-3 shadow-sm"
                            >
                                {({ processing, errors }) => (
                                    <>
                                        <div className="flex flex-col">
                                            <span className="text-base font-medium">
                                                {membership.organization.name}
                                            </span>
                                            <span className="text-sm text-muted-foreground">
                                                {membership.role_label}
                                            </span>
                                        </div>
                                        <div className="flex flex-col items-end gap-2">
                                            <input
                                                type="hidden"
                                                name="organization_id"
                                                value={membership.organization.id}
                                            />
                                            <Button type="submit" disabled={processing}>
                                                {processing && (
                                                    <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />
                                                )}
                                                Enter workspace
                                            </Button>
                                            <InputError message={errors.organization_id} />
                                        </div>
                                    </>
                                )}
                            </Form>
                        ))
                    ) : (
                        <div className="rounded-lg border border-dashed border-muted px-4 py-8 text-center text-sm text-muted-foreground">
                            You are not a member of any organization yet.{' '}
                            <TextLink href={register()}>Create a new organization</TextLink>{' '}
                            to get started.
                        </div>
                    )}
                </div>

                <div className="rounded-md bg-muted/40 p-4 text-sm text-muted-foreground">
                    You can switch organizations at any time from the profile menu once inside the
                    app.
                </div>

                <div className="text-center text-sm text-muted-foreground">
                    Need a fresh workspace?{' '}
                    <TextLink href={register()}>Create a new organization</TextLink>.
                </div>
            </div>
        </AuthLayout>
    );
}
