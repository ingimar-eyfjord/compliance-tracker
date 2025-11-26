import { useMemo, useState } from 'react';

import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import AuthLayout from '@/layouts/auth-layout';

type Option = { value: string; label: string };
type OrganizationOption = { id: string; name: string; slug: string };

interface RegisterProps {
    organizations: OrganizationOption[];
    roles: Option[];
    planTiers: Option[];
}

const selectClassName =
    'border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:pointer-events-none disabled:opacity-50 md:text-sm';

export default function Register({ organizations, roles, planTiers }: RegisterProps) {
    const defaultRole = useMemo(
        () => roles.find((role) => role.value === 'agent')?.value ?? roles[0]?.value ?? '',
        [roles],
    );

    const defaultPlan = useMemo(
        () => planTiers.find((tier) => tier.value === 'free')?.value ?? planTiers[0]?.value ?? 'free',
        [planTiers],
    );

    const [mode, setMode] = useState<'create' | 'join'>(organizations.length ? 'create' : 'create');
    const [organizationId, setOrganizationId] = useState<string>(organizations[0]?.id ?? '');
    const [role, setRole] = useState<string>(defaultRole);
    const [planTier, setPlanTier] = useState<string>(defaultPlan);

    const handleModeChange = (value: string) => {
        if (!value) return;
        const nextMode = value as 'create' | 'join';
        setMode(nextMode);
    };

    return (
        <AuthLayout
            title="Create an account"
            description="Enter your details below to create your account"
        >
            <Head title="Register" />
            <Form
                {...RegisteredUserController.store.form()}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label>How would you like to get started?</Label>
                                <ToggleGroup
                                    type="single"
                                    value={mode}
                                    onValueChange={handleModeChange}
                                    className="w-full"
                                >
                                    <ToggleGroupItem value="create" className="flex-1">
                                        Create new organization
                                    </ToggleGroupItem>
                                    <ToggleGroupItem
                                        value="join"
                                        className="flex-1"
                                        disabled={organizations.length === 0}
                                    >
                                        Join existing organization
                                    </ToggleGroupItem>
                                </ToggleGroup>
                                <input type="hidden" name="mode" value={mode} />
                                <InputError message={errors.mode} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="name"
                                    name="name"
                                    placeholder="Full name"
                                />
                                <InputError
                                    message={errors.name}
                                    className="mt-2"
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Email address</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    tabIndex={2}
                                    autoComplete="email"
                                    name="email"
                                    placeholder="email@example.com"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    required
                                    tabIndex={3}
                                    autoComplete="new-password"
                                    name="password"
                                    placeholder="Password"
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation">
                                    Confirm password
                                </Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    required
                                    tabIndex={4}
                                    autoComplete="new-password"
                                    name="password_confirmation"
                                    placeholder="Confirm password"
                                />
                                <InputError
                                    message={errors.password_confirmation}
                                />
                            </div>

                            {mode === 'create' && (
                                <>
                                    <div className="grid gap-2">
                                        <Label htmlFor="organization_name">Organization name</Label>
                                        <Input
                                            id="organization_name"
                                            type="text"
                                            name="organization_name"
                                            placeholder="Acme Compliance"
                                            required={mode === 'create'}
                                            tabIndex={5}
                                        />
                                        <InputError message={errors.organization_name} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="plan_tier">Plan tier</Label>
                                        <select
                                            id="plan_tier"
                                            name="plan_tier"
                                            value={planTier}
                                            onChange={(event) => setPlanTier(event.target.value)}
                                            className={selectClassName}
                                        >
                                            {planTiers.map((tier) => (
                                                <option key={tier.value} value={tier.value}>
                                                    {tier.label}
                                                </option>
                                            ))}
                                        </select>
                                        <InputError message={errors.plan_tier} />
                                    </div>
                                </>
                            )}

                            {mode === 'join' && (
                                <>
                                    <div className="grid gap-2">
                                        <Label htmlFor="organization_id">Select organization</Label>
                                        <select
                                            id="organization_id"
                                            name="organization_id"
                                            required={mode === 'join'}
                                            value={organizationId}
                                            onChange={(event) => setOrganizationId(event.target.value)}
                                            className={selectClassName}
                                        >
                                            <option value="" disabled>
                                                Choose an organization
                                            </option>
                                            {organizations.map((org) => (
                                                <option key={org.id} value={org.id}>
                                                    {org.name}
                                                </option>
                                            ))}
                                        </select>
                                        <InputError message={errors.organization_id} />
                                        {organizations.length === 0 && (
                                            <p className="text-sm text-muted-foreground">
                                                No organizations available yet. Create a new organization instead.
                                            </p>
                                        )}
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="role">Choose your role</Label>
                                        <select
                                            id="role"
                                            name="role"
                                            required={mode === 'join'}
                                            value={role}
                                            onChange={(event) => setRole(event.target.value)}
                                            className={selectClassName}
                                        >
                                            {roles.map((roleOption) => (
                                                <option key={roleOption.value} value={roleOption.value}>
                                                    {roleOption.label}
                                                </option>
                                            ))}
                                        </select>
                                        <InputError message={errors.role} />
                                    </div>
                                </>
                            )}

                            <Button
                                type="submit"
                                className="mt-2 w-full"
                                tabIndex={mode === 'create' ? 6 : 8}
                                data-test="register-user-button"
                            >
                                {processing && (
                                    <LoaderCircle className="h-4 w-4 animate-spin" />
                                )}
                                Create account
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            Already have an account?{' '}
                            <TextLink href={login()} tabIndex={6}>
                                Log in
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
