import { z } from 'zod'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { router } from '@inertiajs/react'
import { toast } from 'sonner'
import { useEffect } from 'react'
import { Button } from '@/components/ui/button'
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form'
import {
  Sheet,
  SheetClose,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet'
import { SelectDropdown } from '@/components/select-dropdown'
import { priorities, statuses } from '../data/data'
import { type CaseTableRow } from '../data/schema'

type TasksMutateDrawerProps = {
  open: boolean
  onOpenChange: (open: boolean) => void
  currentRow?: CaseTableRow | null
}

const formSchema = z.object({
  status: z.string().min(1, 'Select a status.'),
  priority: z.string().min(1, 'Select a priority.'),
})

type CaseForm = z.infer<typeof formSchema>

export function TasksMutateDrawer({
  open,
  onOpenChange,
  currentRow,
}: TasksMutateDrawerProps) {
  const isUpdate = !!currentRow

  const form = useForm<CaseForm>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      status: currentRow?.status ?? '',
      priority: currentRow?.priority ?? '',
    },
  })

  useEffect(() => {
    form.reset({
      status: currentRow?.status ?? '',
      priority: currentRow?.priority ?? '',
    })
  }, [currentRow, form])

  const onSubmit = (data: CaseForm) => {
    if (!currentRow) {
      toast.error('Select a case to update')
      return
    }

    router.put(
      `/cases/${currentRow.id}`,
      {
        status: data.status,
        priority: data.priority,
      },
      {
        preserveScroll: true,
        onSuccess: () => {
          toast.success('Case updated')
          onOpenChange(false)
        },
        onError: () => toast.error('Unable to update case'),
      }
    )
  }

  return (
    <Sheet
      open={open}
      onOpenChange={(value) => {
        if (!value) {
          form.reset()
        }
        onOpenChange(value)
      }}
    >
      <SheetContent className='flex flex-col'>
        <SheetHeader className='text-start'>
          <SheetTitle>{isUpdate ? 'Update case' : 'Create case'}</SheetTitle>
          <SheetDescription>
            {isUpdate
              ? 'Adjust the case status or priority.'
              : 'Provide details to create a new case.'}
          </SheetDescription>
        </SheetHeader>
        <Form {...form}>
          <form
            id='case-form'
            onSubmit={form.handleSubmit(onSubmit)}
            className='flex-1 space-y-6 overflow-y-auto px-4'
          >
            <FormField
              control={form.control}
              name='status'
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Status</FormLabel>
                  <FormControl>
                    <SelectDropdown
                      defaultValue={field.value}
                      onValueChange={field.onChange}
                      placeholder='Select status'
                      items={statuses.map((status) => ({
                        label: status.label,
                        value: status.value,
                        icon: status.icon,
                      }))}
                    />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
            <FormField
              control={form.control}
              name='priority'
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Priority</FormLabel>
                  <FormControl>
                    <SelectDropdown
                      defaultValue={field.value}
                      onValueChange={field.onChange}
                      placeholder='Select priority'
                      items={priorities.map((priority) => ({
                        label: priority.label,
                        value: priority.value,
                        icon: priority.icon,
                      }))}
                    />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
          </form>
        </Form>
        <SheetFooter className='gap-2'>
          <SheetClose asChild>
            <Button variant='outline'>Close</Button>
          </SheetClose>
          <Button form='case-form' type='submit'>
            Save changes
          </Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  )
}
