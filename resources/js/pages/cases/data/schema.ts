import { z } from 'zod'

export const caseRowSchema = z.object({
  id: z.string(),
  reference: z.string(),
  status: z.string(),
  priority: z.string(),
  assignee: z.string(),
})

export type CaseTableRow = z.infer<typeof caseRowSchema>
