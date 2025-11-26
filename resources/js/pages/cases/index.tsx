import { useMemo } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import { TasksTable } from './components/tasks-table';
import type { NavigateFn } from '@/hooks/use-table-url-state';
import type { CaseTableRow } from './data/schema';
import { TasksProvider } from './components/tasks-provider'
import { TasksDialogs } from './components/tasks-dialogs';
import { TasksPrimaryButtons } from './components/tasks-primary-buttons'

interface CaseSummary {
    id: string;
    status: string;
    priority: string;
    assignee?: string;
    reference?: string;
}

interface CasesIndexProps {
    cases: CaseSummary[];
    filters: {
        filter?: string;
        status?: string[];
        priority?: string[];
        page?: number;
        pageSize?: number;
    };
}

export default function CasesIndex({ cases, filters }: CasesIndexProps) {
    const searchState = useMemo(() => {
        return {
            filter: filters.filter ?? '',
            status: filters.status ?? [],
            priority: filters.priority ?? [],
            page: filters.page ?? 1,
            pageSize: filters.pageSize ?? 10,
        };
    }, [filters.filter, filters.priority, filters.status, filters.page, filters.pageSize]);

    const navigate: NavigateFn = ({ search, replace }) => {
        const base = { ...searchState };
        let nextParams: Record<string, unknown>;

        if (search === true) {
            nextParams = base;
        } else if (typeof search === 'function') {
            nextParams = { ...base, ...search(base) };
        } else {
            nextParams = search;
        }

        const sanitized: Record<string, unknown> = {};
        for (const [key, value] of Object.entries(nextParams)) {
            if (Array.isArray(value)) {
                if (value.length > 0) {
                    sanitized[key] = value;
                }
            } else if (value !== undefined && value !== null && value !== '') {
                sanitized[key] = value;
            }
        }

        router.get('/cases', sanitized, {
            preserveState: true,
            preserveScroll: true,
            replace: replace ?? true,
        });
    };

    const casesForTable: CaseTableRow[] = cases.map((item) => ({
        id: item.id,
        reference: item.reference ?? item.id,
        status: item.status,
        priority: item.priority,
        assignee: item.assignee ?? '—',
    }));

    return (
        <AppLayout breadcrumbs={[{ title: 'Cases', href: '/cases' }]}> 
            <Head title="Cases" />
            <div className="p-6">
                <TasksProvider>
                    <div className="flex flex-wrap items-end justify-between gap-2 pb-6">
                        <div className="flex flex-col gap-2">
                            <h2 className="text-2xl font-bold tracking-tight">Cases</h2>
                            <p className="text-muted-foreground">
                                Review, filter, and search current cases for your organization.
                            </p>
                        </div>
                        <TasksPrimaryButtons />
                    </div>
                    <TasksTable
                        data={casesForTable}
                        searchState={{
                            search: searchState,
                            navigate,
                        }}
                    />
                    <TasksDialogs />
                </TasksProvider>
            </div>
        </AppLayout>
    );
}
