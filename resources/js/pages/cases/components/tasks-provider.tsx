import React, { useCallback, useState } from 'react'
import { type CaseTableRow } from '../data/schema'

type TasksDialogType = 'create' | 'update' | 'delete' | 'import'

type TasksContextType = {
  open: TasksDialogType | null
  openDialog: (dialog: TasksDialogType, row?: CaseTableRow | null) => void
  closeDialog: () => void
  currentRow: CaseTableRow | null
  setCurrentRow: React.Dispatch<React.SetStateAction<CaseTableRow | null>>
}

const TasksContext = React.createContext<TasksContextType | null>(null)

export function TasksProvider({ children }: { children: React.ReactNode }) {
  const [open, setOpen] = useState<TasksDialogType | null>(null)
  const [currentRow, setCurrentRow] = useState<CaseTableRow | null>(null)

  const openDialog = useCallback(
    (dialog: TasksDialogType, row: CaseTableRow | null = null) => {
      setCurrentRow(row)
      setOpen(dialog)
    },
    []
  )

  const closeDialog = useCallback(() => {
    setOpen(null)
    setCurrentRow(null)
  }, [])

  return (
    <TasksContext.Provider
      value={{ open, openDialog, closeDialog, currentRow, setCurrentRow }}
    >
      {children}
    </TasksContext.Provider>
  )
}

export const useTasks = () => {
  const tasksContext = React.useContext(TasksContext)

  if (!tasksContext) {
    throw new Error('useTasks has to be used within <TasksContext>')
  }

  return tasksContext
}
