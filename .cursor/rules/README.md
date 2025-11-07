# Cursor Rules Documentation

This directory contains comprehensive cursorrules (`.mdc` files) that guide AI assistants in working with this Laravel project. These rules are consistent with `AGENTS.md` and `.ai/AI-GUIDELINES.md`.

## File Structure

```log
.cursor/rules/
├── README.md (this file)
├── 00-persona.mdc              # Core AI persona and communication style
├── 01-ai-guidelines.mdc        # AI-GUIDELINES orchestration policy
├── byterover-rules.mdc         # ByteRover MCP integration rules
├── laravel-boost.mdc           # Laravel Boost workflow guidelines
└── modes/
    ├── architect.mdc           # Solution Architect mode
    ├── product-manager.mdc     # Product Manager mode
    └── tester.mdc              # Test Engineer mode
```

## Always Applied Rules

The following rules are always applied (`alwaysApply: true`):

- **00-persona.mdc**: Core AI persona, communication style, and decision-making protocols
- **01-ai-guidelines.mdc**: AI-GUIDELINES orchestration policy and compliance requirements
- **byterover-rules.mdc**: ByteRover MCP knowledge management tools
- **laravel-boost.mdc**: Laravel Boost workflow guidelines and tool usage

## Mode-Specific Rules

Mode-specific rules are applied on-demand (`alwaysApply: false`). Activate them by referencing the mode in your conversation.

### Architect Mode

**Activation**: Request architectural guidance, system design, scalability planning, or integration patterns.

**Use Cases**:

- System architecture design
- Scalability planning
- Technology evaluation
- Integration patterns
- Risk assessment
- Architecture documentation

**Example Activation**:

```markdown
"I need help designing the architecture for a new feature. Please use architect mode."
```

### Product Manager Mode

**Activation**: Request product requirements, feature prioritization, user stories, or roadmap planning.

**Use Cases**:

- Product Requirements Documents (PRDs)
- User story creation
- Feature prioritization
- Roadmap planning
- Success metrics definition
- Stakeholder communication

**Example Activation**:

```markdown
"I need to create a PRD for a new feature. Please use product manager mode."
```

### Test Engineer Mode

**Activation**: Request test strategy, test case creation, test automation, or quality assurance.

**Use Cases**:

- Test strategy development
- Test case creation
- Test automation
- Quality assurance planning
- Bug investigation
- Test coverage analysis
- Performance testing

**Example Activation**:

```markdown
"I need help creating comprehensive tests for a new feature. Please use tester mode."
```

## Usage Examples

### Example 1: Creating a Feature with Full Workflow

```markdown
User: "I want to add a user profile feature. Let's start with a PRD."

AI: [Activates Product Manager mode]
    - Asks clarifying questions
    - Creates PRD document
    - Defines user stories and acceptance criteria

User: "Now let's design the architecture."

AI: [Activates Architect mode]
    - Designs system architecture
    - Creates architecture diagrams
    - Defines integration points
    - Assesses risks

User: "Let's implement it."

AI: [Uses standard development mode with Laravel Boost guidelines]
    - Implements feature following Laravel best practices
    - Uses appropriate tools (search-docs, artisan commands, etc.)

User: "Now let's create comprehensive tests."

AI: [Activates Tester mode]
    - Creates test strategy
    - Writes unit, feature, and integration tests
    - Ensures 90%+ coverage
    - Sets up test automation
```

### Example 2: Quick Feature Addition

```markdown
User: "Add a simple contact form with validation and email sending."

AI: [Uses standard development mode]
    - Implements feature
    - Creates tests (automatically applies testing best practices)
    - Ensures compliance with all guidelines
```

### Example 3: Architecture Review

```markdown
User: "Review the current architecture and suggest improvements for scalability."

AI: [Activates Architect mode]
    - Analyzes current architecture
    - Identifies scalability bottlenecks
    - Proposes improvements with trade-offs
    - Creates architecture diagrams
    - Provides implementation roadmap
```

## Mode Combination

You can combine modes for complex tasks:

```markdown
User: "I need to add a new payment feature. Let's do a full workflow: PRD, architecture, implementation, and testing."

AI: [Sequentially applies modes]
    1. Product Manager mode: Creates PRD
    2. Architect mode: Designs architecture
    3. Standard mode: Implements feature
    4. Tester mode: Creates comprehensive tests
```

## Core Principles

All modes follow these core principles:

1. **Junior Developer Focus**: All guidance is suitable for junior developers (6 months - 2 years experience)
2. **Clear and Actionable**: Provide specific, implementable guidance
3. **Visual Learning**: Use diagrams and visual aids where appropriate
4. **Comprehensive Testing**: Ensure 90%+ test coverage
5. **Security First**: Apply security principles throughout
6. **Performance Conscious**: Consider performance implications
7. **Documentation**: Document decisions and patterns

## Compliance

All rules ensure compliance with:

- **AGENTS.md**: Laravel Boost workflow guidelines
- **AI-GUIDELINES.md**: Comprehensive development standards
- **AI-GUIDELINES/**: Technology-specific implementation guides

## Customization

To customize these rules:

1. Edit the relevant `.mdc` file
2. Maintain consistency with `AGENTS.md` and `.ai/AI-GUIDELINES.md`
3. Test the changes with actual AI interactions
4. Update this README if adding new modes or significant changes

## References

- [AGENTS.md](../../AGENTS.md) - Laravel Boost workflow guidelines
- [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) - Comprehensive development standards
- [.ai/AI-GUIDELINES/](../../.ai/AI-GUIDELINES/) - Technology-specific guides

## Support

For questions or issues with these rules:

1. Review the relevant `.mdc` file
2. Check `AGENTS.md` and `.ai/AI-GUIDELINES.md` for context
3. Consult the mode-specific documentation in the `modes/` directory
