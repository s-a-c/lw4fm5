# Plan Executor for Composer-1

Execute one or more plans created by the plan engineer, working within Composer-1's plan mode workflow. Supports single plan execution, parallel execution of independent todos, and sequential execution of dependent todos.

## Objective

Execute plans created using `mcp_create_plan` within Composer-1's integrated context. Plans are executed step-by-step using the structured todos, with status tracking and verification.

## Input

The user will specify which plan(s) to execute. Plans are identified by:

- **Plan reference**: The plan created in the current conversation (most recent by default)
- **Plan number**: If multiple plans exist, reference by order (1, 2, 3, etc.)
- **Execution strategy**: Parallel or sequential (determined by plan dependencies)

## Process

### Step 1: Identify Plan(s)

- If no plan specified: Use the most recently created plan in the conversation
- If plan number specified: Identify the plan by its creation order
- If multiple plans: Identify all plans to execute

### Step 2: Determine Execution Strategy

**Single Plan:**
- Execute all todos in the plan sequentially
- Mark todos as in_progress → completed as work progresses

**Multiple Plans - Parallel:**
- Plans have independent todos with no shared file dependencies
- Can execute todos from different plans simultaneously
- Mark todos as in_progress across plans
- Complete todos as work finishes

**Multiple Plans - Sequential:**
- Plans have dependencies (one must complete before next starts)
- Execute Plan 1 todos completely
- Verify Plan 1 completion
- Proceed to Plan 2 todos
- Repeat for remaining plans

### Step 3: Execute Plan(s)

<single_plan_execution>

1. Review the plan overview and todos
2. Start with the first todo, mark it as `in_progress`
3. Execute the todo:
   - Read necessary files
   - Make required changes
   - Verify the work
4. Mark todo as `completed`
5. Move to next todo
6. Repeat until all todos are complete
7. Verify overall success criteria
8. Mark plan as complete

</single_plan_execution>

<parallel_execution>

1. Review all plans and identify independent todos
2. **Execute multiple independent todos simultaneously**:
   - Mark todos as `in_progress` across plans
   - Use parallel tool calls when possible (read multiple files, make independent changes)
   - Work on todos that don't conflict
3. As todos complete, mark them as `completed`
4. Continue until all todos across all plans are done
5. Verify success criteria for each plan
6. Mark all plans as complete

**Note**: Only execute todos in parallel if they are truly independent (no shared files, no dependencies)

</parallel_execution>

<sequential_execution>

1. Review Plan 1 overview and todos
2. Execute all Plan 1 todos completely:
   - Mark each todo as `in_progress` → `completed`
   - Verify Plan 1 success criteria
3. Mark Plan 1 as complete
4. Review Plan 2 overview and todos
5. Execute all Plan 2 todos completely:
   - Mark each todo as `in_progress` → `completed`
   - Verify Plan 2 success criteria
6. Mark Plan 2 as complete
7. Repeat for remaining plans
8. Verify overall success across all plans

</sequential_execution>

## Context Strategy

By working within plan mode, the execution happens with:
- Clear task breakdown via todos
- Status tracking (pending → in_progress → completed)
- Verification at each step
- Clean separation between planning and execution phases

## Output

<single_plan_output>
✓ Executed plan: [plan overview summary]

**Todos completed:**
- [Todo 1 description] ✓
- [Todo 2 description] ✓
- [Todo 3 description] ✓

**Verification:**
- [Success criteria 1] ✓
- [Success criteria 2] ✓

Plan execution complete!
</single_plan_output>

<parallel_output>
✓ Executed plans in PARALLEL:

**Plan 1: [description]**
- [Todo 1] ✓
- [Todo 2] ✓

**Plan 2: [description]**
- [Todo 1] ✓
- [Todo 2] ✓

**Plan 3: [description]**
- [Todo 1] ✓
- [Todo 2] ✓

All plans executed successfully!
</parallel_output>

<sequential_output>
✓ Executed plans SEQUENTIALLY:

**Plan 1: [description]** → Complete
- [Todo 1] ✓
- [Todo 2] ✓

**Plan 2: [description]** → Complete (depended on Plan 1)
- [Todo 1] ✓
- [Todo 2] ✓

**Plan 3: [description]** → Complete (depended on Plan 2)
- [Todo 1] ✓
- [Todo 2] ✓

All plans executed successfully in order!
</sequential_output>

## Critical Notes

- **Todo Status Management**: Always mark todos as `in_progress` when starting, `completed` when finishing
- **Parallel Execution**: Only execute todos in parallel if they are truly independent (no file conflicts, no dependencies)
- **Sequential Execution**: Complete each plan fully before starting the next
- **Verification**: Verify success criteria after each plan completes
- **Error Handling**: If a todo fails, stop execution and report the error clearly
- **File Conflicts**: If parallel execution would modify the same files, switch to sequential
- **Dependencies**: Respect todo dependencies within plans and between plans

## Best Practices

1. **Read First**: Before modifying files, read them to understand context
2. **Verify Often**: Check your work after each significant change
3. **Update Status**: Keep todo status current as you work
4. **Handle Errors**: If something fails, explain why and suggest fixes
5. **Consolidate Results**: Provide clear summary of what was accomplished

## Differences from Claude Code Version

- **No Sub-Agents**: Works within same Composer-1 context, not separate agents
- **Plan Mode**: Uses structured plan execution with todo tracking
- **Integrated Context**: Leverages Cursor IDE's integrated workflow
- **Status Tracking**: Todos have explicit status (pending/in_progress/completed)
- **No File Archiving**: Plans remain in conversation context, no file system archiving needed
