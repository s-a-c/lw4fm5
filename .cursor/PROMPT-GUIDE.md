# Comprehensive Prompt Guide for Cursor Rules

This guide demonstrates how to effectively use the cursorrules system to get the best results from AI assistants.

## Understanding the System

The cursorrules system consists of:

1. **Always Applied Rules**: Core persona, guidelines, and workflows (automatically active)
2. **Mode-Specific Rules**: Specialized expertise modes (activated on-demand)
3. **Project Guidelines**: Laravel Boost and AI-GUIDELINES compliance

## Effective Prompt Patterns

### Pattern 1: Single Mode Activation

**Structure**: Clear request + mode specification

```markdown
I need to [task]. Please use [mode] mode.

Example:
"I need to design the architecture for a new payment system. Please use architect mode."
```

**Why it works**:

- Clear task definition
- Explicit mode activation
- Focused expertise application

### Pattern 2: Sequential Mode Workflow

**Structure**: Multi-step process with mode transitions

```markdown
Step 1: [Mode 1] - [Task 1]
Step 2: [Mode 2] - [Task 2]
Step 3: [Mode 3] - [Task 3]

Example:
"Let's build a new feature end-to-end:
1. Product Manager mode: Create a PRD for user notifications
2. Architect mode: Design the notification system architecture
3. Standard mode: Implement the feature
4. Tester mode: Create comprehensive tests"
```

**Why it works**:

- Complete workflow coverage
- Appropriate expertise at each stage
- Clear progression

### Pattern 3: Context-Rich Requests

**Structure**: Background + specific request + mode

```markdown
Context: [Background information]
Request: [Specific task]
Mode: [Mode to use]

Example:
"Context: We're building a Laravel 12 application with Livewire and need to add
real-time notifications. The system currently uses database polling.

Request: Design a scalable notification architecture that supports multiple
notification channels (email, SMS, push) and can handle 10,000+ concurrent users.

Mode: Please use architect mode."
```

**Why it works**:

- Provides necessary context
- Sets clear requirements
- Enables informed recommendations

### Pattern 4: Comparative Analysis

**Structure**: Request comparison + mode

```markdown
I need to compare [options] for [purpose]. Please use [mode] mode and provide
recommendations with confidence scores.

Example:
"I need to compare different caching strategies (Redis, Memcached, database)
for our Laravel application. Please use architect mode and provide recommendations
with confidence scores."
```

**Why it works**:

- Enables informed decision-making
- Provides multiple perspectives
- Includes confidence scoring

## Mode-Specific Prompt Examples

### Architect Mode Prompts

#### System Design

```markdown
"Design a microservices architecture for our e-commerce platform. Consider:
- User management service
- Product catalog service
- Order processing service
- Payment service
- Notification service

Please use architect mode and include:
- Service boundaries
- Communication patterns
- Data consistency strategies
- Scalability considerations
- Risk assessment"
```

#### Scalability Planning

```markdown
"Our Laravel application is experiencing performance issues under load.
Please use architect mode to:
1. Analyze current architecture
2. Identify bottlenecks
3. Propose scalability improvements
4. Provide implementation roadmap
5. Assess risks and mitigation strategies"
```

#### Technology Evaluation

```markdown
"We need to choose between Laravel Queues with Redis vs. RabbitMQ for our
background job processing. Please use architect mode to:
- Compare both options
- Evaluate against our requirements (10,000+ jobs/hour, reliability, monitoring)
- Provide recommendation with confidence score
- Include migration considerations"
```

### Product Manager Mode Prompts

#### PRD Creation

```markdown
"Please use product manager mode to create a PRD for a two-factor authentication
feature. I need:
- User stories for enabling/disabling 2FA
- Acceptance criteria
- Success metrics
- Non-goals
- Technical considerations"
```

#### Feature Prioritization

```markdown
"We have a backlog of 20 features. Please use product manager mode to:
1. Categorize features by impact and effort
2. Prioritize based on user value and business goals
3. Create a roadmap for the next 3 quarters
4. Identify dependencies and risks"
```

#### User Story Development

```markdown
"Please use product manager mode to create user stories for a user profile
management feature. Include:
- Stories for viewing, editing, and deleting profiles
- Stories for profile privacy settings
- Acceptance criteria for each story
- Edge cases and error scenarios"
```

### Tester Mode Prompts

#### Test Strategy

```markdown
"Please use tester mode to create a comprehensive test strategy for our new
API endpoints. Include:
- Unit tests for models and services
- Feature tests for API endpoints
- Integration tests for external services
- Performance tests
- Security tests
- Test coverage goals (90% minimum)"
```

#### Test Case Creation

```markdown
"Please use tester mode to create test cases for the user authentication flow:
- Registration
- Login
- Password reset
- Email verification
- Two-factor authentication

Include happy paths, failure paths, and edge cases."
```

#### Bug Investigation

```markdown
"We're experiencing intermittent failures in our payment processing. Please use
tester mode to:
1. Create a test that reproduces the bug
2. Identify root cause
3. Propose fix
4. Create additional tests to prevent regression"
```

## Advanced Prompt Patterns

### Pattern 5: Iterative Refinement

```markdown
Initial: "Please use architect mode to design a notification system."

Refinement 1: "Based on your design, please add error handling and retry logic."

Refinement 2: "Now add monitoring and alerting capabilities."

Refinement 3: "Finally, create an implementation roadmap with phases."
```

### Pattern 6: Multi-Perspective Analysis

```markdown
"Please analyze our user authentication system from three perspectives:
1. Architect mode: System design and scalability
2. Tester mode: Test coverage and quality assurance
3. Product Manager mode: User experience and feature completeness

Provide a comprehensive analysis with recommendations from each perspective."
```

### Pattern 7: Constraint-Based Design

```markdown
"Please use architect mode to design a notification system with these constraints:
- Must work with Laravel 12
- Must support 100,000+ notifications/day
- Must have <100ms latency
- Must be cost-effective (<$50/month)
- Must be maintainable by a small team

Provide design options with trade-offs."
```

## Best Practices

### 1. Be Specific

**Bad**: "Help me with testing"
**Good**: "Please use tester mode to create feature tests for the user registration endpoint, including validation, error handling, and success scenarios"

### 2. Provide Context

**Bad**: "Design an API"
**Good**: "Design a REST API for user management in our Laravel 12 application. We need endpoints for CRUD operations, authentication, and role-based access control. Please use architect mode."

### 3. Set Clear Expectations

**Bad**: "Create a PRD"
**Good**: "Please use product manager mode to create a PRD for a file upload feature. Include user stories, acceptance criteria, success metrics, and technical considerations. Target audience is junior developers."

### 4. Request Visual Aids

**Bad**: "Explain the architecture"
**Good**: "Please use architect mode to explain the system architecture with Mermaid diagrams showing component relationships and data flow"

### 5. Ask for Confidence Scores

**Bad**: "What's the best approach?"
**Good**: "Please use architect mode to recommend the best caching strategy for our use case, with confidence scores and trade-off analysis"

### 6. Request Step-by-Step Guidance

**Bad**: "How do I implement this?"
**Good**: "Please provide step-by-step implementation guidance suitable for a junior developer, including code examples and validation steps"

## Common Mistakes to Avoid

### Mistake 1: Not Specifying Mode

**Problem**: AI uses default mode, may not apply specialized expertise
**Solution**: Always specify the mode when you need specialized expertise

### Mistake 2: Vague Requests

**Problem**: AI doesn't understand what you need
**Solution**: Be specific about requirements, constraints, and expected output

### Mistake 3: Missing Context

**Problem**: AI makes assumptions that may not fit your situation
**Solution**: Provide relevant context about your project, constraints, and goals

### Mistake 4: Not Requesting Visualizations

**Problem**: Complex concepts are harder to understand without diagrams
**Solution**: Request diagrams, especially for architecture and workflows

### Mistake 5: Skipping Testing

**Problem**: Code is written without adequate tests
**Solution**: Always request test creation, or the AI will remind you (tester mode)

## Example Workflows

### Workflow 1: New Feature Development

```markdown
1. "Please use product manager mode to create a PRD for [feature]"
   → AI creates PRD with user stories and acceptance criteria

2. "Please use architect mode to design the architecture for [feature]"
   → AI designs architecture with diagrams and risk assessment

3. "Please implement [feature] based on the PRD and architecture"
   → AI implements feature following Laravel best practices

4. "Please use tester mode to create comprehensive tests for [feature]"
   → AI creates tests with 90%+ coverage

5. "Please review the implementation and suggest improvements"
   → AI reviews code and suggests optimizations
```

### Workflow 2: Performance Optimization

```markdown
1. "Please use architect mode to analyze our current architecture for
   performance bottlenecks"
   → AI identifies bottlenecks and proposes solutions

2. "Please implement the recommended optimizations"
   → AI implements optimizations

3. "Please use tester mode to create performance tests"
   → AI creates performance tests with benchmarks

4. "Please verify the optimizations improved performance"
   → AI runs tests and validates improvements
```

### Workflow 3: Bug Investigation

```markdown
1. "We're experiencing [bug description]. Please use tester mode to investigate"
   → AI creates test to reproduce bug

2. "Please identify the root cause and propose a fix"
   → AI identifies cause and proposes solution

3. "Please implement the fix"
   → AI implements fix

4. "Please create additional tests to prevent regression"
   → AI creates comprehensive regression tests
```

## Tips for Maximum Effectiveness

1. **Start Broad, Then Narrow**: Begin with high-level requests, then drill down into specifics
2. **Use Iterative Refinement**: Build on previous responses to refine solutions
3. **Request Multiple Perspectives**: Get architect, tester, and product manager views when appropriate
4. **Ask for Explanations**: Request explanations suitable for junior developers
5. **Request Visual Aids**: Always ask for diagrams when dealing with complex concepts
6. **Validate Understanding**: Ask the AI to confirm understanding before proceeding
7. **Request Confidence Scores**: Get confidence percentages for recommendations
8. **Ask for Trade-offs**: Understand pros and cons of different approaches

## Conclusion

Effective use of the cursorrules system requires:

- **Clear Communication**: Be specific about what you need
- **Mode Selection**: Choose the right mode for the task
- **Context Provision**: Provide relevant background information
- **Iterative Refinement**: Build on previous interactions
- **Comprehensive Coverage**: Use multiple modes for complex tasks

By following these patterns and best practices, you'll get the most value from the AI assistant while maintaining consistency with project guidelines and standards.
