import {
  ArrowDown,
  ArrowRight,
  ArrowUp,
  CheckCircle,
  Timer,
  HelpCircle,
  CircleOff,
} from 'lucide-react'



export const statuses = [
  {
    label: 'New',
    value: 'new' as const,
    icon: Timer,
  },
  {
    label: 'Triage',
    value: 'triage' as const,
    icon: HelpCircle,
  },
  {
    label: 'In Progress',
    value: 'in_progress' as const,
    icon: Timer,
  },
  {
    label: 'Resolved',
    value: 'resolved' as const,
    icon: CheckCircle,
  },
  {
    label: 'Closed',
    value: 'closed' as const,
    icon: CircleOff,
  },
]

export const priorities = [
  {
    label: 'Low',
    value: 'low' as const,
    icon: ArrowDown,
  },
  {
    label: 'Medium',
    value: 'medium' as const,
    icon: ArrowRight,
  },
  {
    label: 'High',
    value: 'high' as const,
    icon: ArrowUp,
  },
]
