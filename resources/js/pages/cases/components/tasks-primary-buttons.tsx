import { Download, Plus } from 'lucide-react'
import { router } from '@inertiajs/react'
import { toast } from 'sonner'
import { Button } from '@/components/ui/button'
import { useTasks } from './tasks-provider'

export function TasksPrimaryButtons() {
  const { openDialog } = useTasks()
  return (
    <div className='flex gap-2'>
      <Button
        variant='outline'
        className='space-x-1'
        onClick={() => openDialog('import')}
      >
        <span>Import</span> <Download size={18} />
      </Button>
      <Button
        className='space-x-1'
        onClick={() => {
          toast.info('Redirecting to case creation')
          router.visit('/cases/create')
        }}
      >
        <span>Create</span> <Plus size={18} />
      </Button>
    </div>
  )
}
