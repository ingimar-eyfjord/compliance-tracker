import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Analytics } from '@/components/dashboard/analytics';
import { RecentCases } from '@/components/dashboard/recent-cases';
import { Overview } from '@/components/dashboard/overview';
// import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
interface DashboardProps {
    allCases: {
        id: string;
        report_id: string;
        priority: string;
        status: string;
        tags: string[];
        created_at: string,
        assignee: {
            id: string;
            name: string;
            email: string;
        };
    }[];
    allChannels: {
        id: string;
        name: string;
        status: string;
    }[];
    allReports: {
        id: string;
        channel_id?: string | null;
        status: string;
    }[];
}
const view = "overview"

export default function Dashboard({ allCases, allChannels, allReports }: DashboardProps) {

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div className="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <Card>
                            <CardHeader className='flex flex-row items-center justify-between space-y-0 pb-2'>
                                <CardTitle className='text-sm font-medium'>
                                    Channels
                                </CardTitle>
                                <svg
                                    xmlns='http://www.w3.org/2000/svg'
                                    viewBox='0 0 24 24'
                                    fill='none'
                                    stroke='currentColor'
                                    strokeLinecap='round'
                                    strokeLinejoin='round'
                                    strokeWidth='2'
                                    className='text-muted-foreground h-4 w-4'
                                >
                                    <path d='M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6' />
                                </svg>
                            </CardHeader>
                            <CardContent>
                                <div className='text-2xl font-bold'>{allChannels.length}</div>
                                <p className='text-muted-foreground text-xs'>
                                    +20.1% from last month
                                </p>
                            </CardContent>
                        </Card>
                    </div>
                    <div className="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <Card>
                            <CardHeader className='flex flex-row items-center justify-between space-y-0 pb-2'>
                                <CardTitle className='text-sm font-medium'>
                                    Reports
                                </CardTitle>
                                <svg
                                    xmlns='http://www.w3.org/2000/svg'
                                    viewBox='0 0 24 24'
                                    fill='none'
                                    stroke='currentColor'
                                    strokeLinecap='round'
                                    strokeLinejoin='round'
                                    strokeWidth='2'
                                    className='text-muted-foreground h-4 w-4'
                                >
                                    <path d='M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6' />
                                </svg>
                            </CardHeader>
                            <CardContent>
                                <div className='text-2xl font-bold'>{allReports.length}</div>
                                <p className='text-muted-foreground text-xs'>
                                    +20.1% from last month
                                </p>
                            </CardContent>
                        </Card>
                    </div>
                    <div className="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <Card>
                            <CardHeader className='flex flex-row items-center justify-between space-y-0 pb-2'>
                                <CardTitle className='text-sm font-medium'>
                                    Total Cases
                                </CardTitle>
                                <svg
                                    xmlns='http://www.w3.org/2000/svg'
                                    viewBox='0 0 24 24'
                                    fill='none'
                                    stroke='currentColor'
                                    strokeLinecap='round'
                                    strokeLinejoin='round'
                                    strokeWidth='2'
                                    className='text-muted-foreground h-4 w-4'
                                >
                                    <path d='M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6' />
                                </svg>
                            </CardHeader>
                            <CardContent>
                                <div className='text-2xl font-bold'>{allCases.length}</div>
                                <p className='text-muted-foreground text-xs'>
                                    +20.1% from last month
                                </p>
                            </CardContent>
                        </Card>
                    </div>
                </div>
                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">

                    {view === "overview" ?
                        <div className='grid grid-cols-1 gap-4 lg:grid-cols-7'>
                            <Card className='col-span-1 lg:col-span-4'>
                                <CardHeader>
                                    <CardTitle>Case Overview</CardTitle>
                                </CardHeader>
                                <CardContent className='ps-2'>
                                    <Overview allCases={allCases} />
                                </CardContent>
                            </Card>
                            <Card className='col-span-1 lg:col-span-3'>
                                <CardHeader>
                                    <CardTitle>Recent Cases</CardTitle>
                                    <CardDescription>
                                        {allCases.filter((caseItem) => new Date(caseItem.created_at).toLocaleString('default', { month: 'short' }) === new Date().toLocaleString('default', { month: 'short' })).length} cases opened this month
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <RecentCases allCases={allCases} />
                                </CardContent>
                            </Card>
                        </div>
                        :
                        <Analytics />
                    }

                </div>
            </div>

        </AppLayout>
    );
}
