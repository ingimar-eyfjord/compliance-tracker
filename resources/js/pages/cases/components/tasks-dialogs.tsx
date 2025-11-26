import { router } from '@inertiajs/react'
import { toast } from 'sonner'
import { ConfirmDialog } from '@/components/confirm-dialog'
import { TasksImportDialog } from './tasks-import-dialog'
import { TasksMutateDrawer } from './tasks-mutate-drawer'
import { useTasks } from './tasks-provider'

export function TasksDialogs() {
  const { open, closeDialog, currentRow } = useTasks()

  return (
    <>
      <TasksMutateDrawer
        key='case-update'
        open={open === 'update'}
        currentRow={currentRow}
        onOpenChange={(isOpen) => {
          if (!isOpen) closeDialog()
        }}
      />

      <TasksImportDialog
        key='cases-import'
        open={open === 'import'}
        onOpenChange={(isOpen) => {
          if (!isOpen) closeDialog()
        }}
      />

      <ConfirmDialog
        key='case-delete'
        destructive
        open={open === 'delete'}
        onOpenChange={(isOpen) => {
          if (!isOpen) closeDialog()
        }}
        handleConfirm={() => {
          if (!currentRow) return
          router.delete(`/cases/${currentRow.id}`, {
            preserveScroll: true,
            onSuccess: () => {
              toast.success('Case deleted')
              closeDialog()
            },
            onError: () => {
              toast.error('Unable to delete case')
              closeDialog()
            },
          })
        }}
        className='max-w-md'
        title={`Delete this case: ${currentRow?.reference ?? currentRow?.id ?? ''}?`}
        desc='This action cannot be undone.'
        confirmText='Delete'
      />
    </>
  )
}
