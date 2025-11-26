import { Bar, BarChart, ResponsiveContainer, XAxis, YAxis } from 'recharts'


interface OverviewProps {
    allCases: {
        id: string;
        report_id: string;
        priority: string;
        status: string;
        tags: string[];
        created_at: string,
        assignee?: {
            id: string;
            name: string;
            email: string;
        };
    }[];
}

export function Overview({ allCases }: OverviewProps) {


    const dataMap = allCases.map((c) => { 
        const date = new Date(c.created_at);
        const month = date.toLocaleString('default', { month: 'short' });
        const numberCases = allCases.filter((caseItem) => {
            const caseDate = new Date(caseItem.created_at);
            const caseMonth = caseDate.toLocaleString('default', { month: 'short' });
            return caseMonth === month;
        }).length;
        return { month, numberCases };
    })

    const data = [
        {
            name: 'Jan',
            total: dataMap.filter(d => d.month === 'Jan')[0]?.numberCases || 0,
        },
        {
            name: 'Feb',
            total: dataMap.filter(d => d.month === 'Feb')[0]?.numberCases || 0,
        },
        {
            name: 'Mar',
            total: dataMap.filter(d => d.month === 'Mar')[0]?.numberCases || 0,
        },
        {
            name: 'Apr',
            total: dataMap.filter(d => d.month === 'Apr')[0]?.numberCases || 0,
        },
        {
            name: 'May',
            total: dataMap.filter(d => d.month === 'May')[0]?.numberCases || 0,
        },
        {
            name: 'Jun',
            total: dataMap.filter(d => d.month === 'Jun')[0]?.numberCases || 0,
        },
        {
            name: 'Jul',
            total: dataMap.filter(d => d.month === 'Jul')[0]?.numberCases || 0,
        },
        {
            name: 'Aug',
            total: dataMap.filter(d => d.month === 'Aug')[0]?.numberCases || 0,
        },
        {
            name: 'Sep',
            total: dataMap.filter(d => d.month === 'Sep')[0]?.numberCases || 0,
        },
        {
            name: 'Oct',
            total: dataMap.filter(d => d.month === 'Oct')[0]?.numberCases || 0,
        },
        {
            name: 'Nov',
            total: dataMap.filter(d => d.month === 'Nov')[0]?.numberCases || 0,
        },
        {
            name: 'Dec',
            total: dataMap.filter(d => d.month === 'Dec')[0]?.numberCases || 0,
        },
    ]


    return (
        <ResponsiveContainer width='100%' height={350}>
            <BarChart data={data}>
                <XAxis
                    dataKey='name'
                    stroke='#888888'
                    fontSize={12}
                    tickLine={false}
                    axisLine={false}
                />
                <YAxis
                    stroke='#888888'
                    fontSize={12}
                    tickLine={false}
                    axisLine={false}
                    tickFormatter={(value) => `$${value}`}
                />
                <Bar
                    dataKey='total'
                    fill='currentColor'
                    radius={[4, 4, 0, 0]}
                    className='fill-primary'
                />
            </BarChart>
        </ResponsiveContainer>
    )
}
