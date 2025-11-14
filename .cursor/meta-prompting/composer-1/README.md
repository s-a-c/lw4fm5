# Meta-Prompting System for Composer-1 (Cursor IDE)

A systematic approach to building complex software with Composer-1 AI Agent by delegating plan engineering to the AI itself.

## The Problem

When building complex features, most people either:

- Write vague requests → get mediocre results → iterate 20+ times
- Spend hours crafting detailed plans manually
- Pollute their main context window with exploration, analysis, and implementation all mixed together

## The Solution

This system separates **analysis** from **execution**:

1. **Analysis Phase** (main context): Tell Composer-1 what you want in natural language. It asks clarifying questions, analyzes your codebase, and generates a rigorous, specification-grade plan using structured todos.

2. **Execution Phase** (plan mode): The generated plan runs in plan mode, producing high-quality implementation on the first try with clear task breakdown and verification.

## What Makes This Effective

The system consistently generates plans with:

- **Structured todos** for clear task breakdown
- **Contextual "why"** - explains purpose, audience, and goals in the overview
- **Success criteria** - specific, measurable outcomes
- **Verification protocols** - how to test that it worked
- **"What to avoid and WHY"** - prevents common mistakes with reasoning
- **Extended thinking triggers** - for complex tasks requiring deep analysis
- **Harmonic weighting** - asks Composer-1 to think about trade-offs and optimal approaches

Most developers don't naturally think through all these dimensions. This system does, every time.

## Installation

1. Place the files in your project's `.cursor/meta-prompting/composer-1/` directory:

   ```bash
   # Files should be at:
   .cursor/meta-prompting/composer-1/README.md
   .cursor/meta-prompting/composer-1/create-plan.md
   .cursor/meta-prompting/composer-1/execute-plan.md
   ```

2. Reference these files when working with Composer-1 in Cursor IDE

3. Verify installation by asking Composer-1 to create a plan for a simple task

## Usage

### Basic Workflow

```bash
# 1. Describe what you want to Composer-1
"I want to build a dashboard for user analytics with real-time graphs"

# 2. Answer clarifying questions (if asked)
# Composer-1 will ask about specifics: data sources, chart types, frameworks, etc.

# 3. Review and confirm
# Composer-1 shows you what it understood and creates a plan with todos

# 4. Confirm the plan
# Composer-1 uses mcp_create_plan tool to create a structured plan

# 5. Execute in plan mode
# Once you confirm, Composer-1 executes the plan step by step
```

### When to Use This

**Use meta-prompting for:**

- Complex refactoring across multiple files
- New features requiring architectural decisions
- Database migrations and schema changes
- Performance optimization requiring analysis
- Any task with 3+ distinct steps

**Skip meta-prompting for:**

- Simple edits (change background color)
- Single-file tweaks
- Obvious, straightforward tasks
- Quick experiments

### Advanced: Multiple Plans

For complex projects, Composer-1 may break your request into multiple plans:

**Parallel execution** (independent tasks):

```bash
# Composer-1 detects independent modules and creates:
# - Plan 1: Implement authentication module
# - Plan 2: Implement API endpoints
# - Plan 3: Implement UI components
#
# These can be executed with todos marked for parallel work
```

**Sequential execution** (dependent tasks):

```bash
# Composer-1 detects dependencies and creates:
# - Plan 1: Setup database schema
# - Plan 2: Create migrations (depends on Plan 1)
# - Plan 3: Seed data (depends on Plan 2)
#
# Todos are ordered sequentially with dependencies
```

### Plan Organization

All plans are created using the `mcp_create_plan` tool and tracked in your conversation context. Plans include:

- **Overview**: High-level description and context
- **Todos**: Structured task breakdown with status tracking
- **Dependencies**: Clear ordering for sequential tasks
- **Verification**: Built-in success criteria

After successful execution, plans are marked complete with all todos finished.

## Why This Works

The system transforms vague ideas into rigorous specifications by:

1. **Asking the right questions** - Clarifies ambiguity before generating anything
2. **Adding structure automatically** - Todos, success criteria, verification steps
3. **Explaining constraints** - Not just "what" but "WHY" things should be done certain ways
4. **Thinking about failure modes** - "What to avoid and why" prevents common mistakes
5. **Defining done** - Clear, measurable success criteria so you know when it's complete

This level of systematic thinking is hard to maintain manually, especially when you're focused on solving the problem itself.

## The Context Advantage

With Composer-1 in Cursor IDE, context management is key to quality results.

**Without meta-prompting:**

- Main window fills with: codebase exploration + requirements gathering + implementation + debugging + iteration
- Context becomes cluttered with analytical work mixed with execution

**With meta-prompting:**

- Main window: Clean requirements gathering and plan creation
- Plan mode: Structured execution with clear todos and verification
- Result: Higher quality implementation, cleaner separation of concerns

## Tips for Best Results

1. **Be conversational in initial request** - Don't try to write a perfect plan yourself, just explain what you want naturally

2. **Answer clarifying questions thoroughly** - The quality of your answers directly impacts the generated plan

3. **Review generated plans** - Check the todos and overview before confirming execution

4. **Trust the system** - It asks "what to avoid and why", defines success criteria, and includes verification steps you might forget

5. **Use parallel todos** - If Composer-1 detects independent tasks, they can be marked for parallel execution

## How It Works Under the Hood

1. **create-plan** analyzes your request using structured thinking:

   - Clarity check (would a colleague understand this?)
   - Task complexity assessment
   - Single vs multiple plans decision
   - Parallel vs sequential execution strategy
   - Reasoning depth needed
   - Project context requirements
   - Verification needs

2. Conditionally includes advanced features:

   - Extended thinking triggers for complex reasoning
   - "Go beyond basics" language for ambitious tasks
   - WHY explanations for constraints
   - Parallel tool calling guidance
   - Reflection after tool use for agentic workflows

3. **execute-plan** works within plan mode:
   - Reads the generated plan todos
   - Executes tasks sequentially or in parallel as marked
   - Updates todo status as work progresses
   - Verifies completion against success criteria
   - Marks plan complete when all todos are done

## Key Differences from Claude Code Version

1. **No Sub-Agents**: Works within the same Composer-1 context using plan mode
2. **Plan Tool**: Uses `mcp_create_plan` instead of writing prompt files
3. **Plan Mode**: User confirms plan, then executes in structured plan mode
4. **Todos**: Breaks down tasks using plan todos instead of XML prompt sections
5. **Cursor Context**: Leverages Cursor IDE's integrated workflow

## Credits

Adapted from the Claude Code meta-prompting system by TÂCHES for systematic, high-quality Composer-1 workflows in Cursor IDE.

---

**Questions or improvements?** Open an issue or submit a PR.
