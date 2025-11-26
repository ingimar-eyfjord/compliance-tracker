
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import {Badge} from '@/components/ui/badge'

interface OverviewProps {
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
}

export function RecentCases({ allCases }: OverviewProps) {


  const cases = allCases.map((c) => {

    const assigneeInitials = c.assignee ? c.assignee.name.split(' ').map(n => n[0]).join('') : '';
    const assigneeEmail = c.assignee ? c.assignee.email : '';
    const assigneeName = c.assignee ? c.assignee.name : '';
    const priority = c.priority;
  
    return { assigneeInitials, assigneeEmail, assigneeName, priority}
  }
  );
  

  return (
    <div className='space-y-8'>
      {cases.map((c) => 
    <div className='flex items-center gap-4'>
        <Avatar className='h-9 w-9'>
          <AvatarImage src='/avatars/01.png' alt='Avatar' />
          <AvatarFallback>{c.assigneeInitials}</AvatarFallback>
        </Avatar>
        <div className='flex flex-1 flex-wrap items-center justify-between'>
          <div className='space-y-1'>
            <p className='text-sm leading-none font-medium'>{c.assigneeName}</p>
            <p className='text-muted-foreground text-sm'>
              {c.assigneeEmail}
            </p>
          </div>
          <Badge color={c.priority == "low"? "green" : c.priority == "medium" ? "orange": "red" }>{c.priority}</Badge>
        </div>
      </div>
      )}




    </div>
  )
}
