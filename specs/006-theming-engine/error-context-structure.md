# Error Context Structure (T027b1, FR-104)

This document defines the error context structure for theme-related errors, ensuring sufficient context for debugging without exposing sensitive data.

## Error Context Fields

All theme-related error logs include the following structured context:

### Application Context
- `timestamp` (string, ISO8601): When the error occurred
- `timezone` (string): Application timezone
- `environment` (string): Application environment (local, staging, production)
- `request_id` (string): Unique request identifier (from `X-Request-ID` header or generated)

### User Context
- `user_id` (int|null): Authenticated user ID (if available)
- `user_authenticated` (bool): Whether user is authenticated

**Excluded**: Email addresses, passwords, API tokens, or any other sensitive user data

### Session Context
- `session_id` (string): Laravel session identifier

### Request Context
- `request_url` (string): Full request URL
- `request_method` (string): HTTP method (GET, POST, etc.)
- `request_ip` (string): Client IP address
- `request_user_agent` (string): User agent string
- `request_referer` (string|null): HTTP Referer header

**Excluded**: Request body, query parameters (unless sanitized), headers containing tokens/secrets

### Exception Context (when exception provided)
- `exception_class` (string): Exception class name
- `exception_message` (string): Exception message
- `exception_code` (int): Exception code
- `exception_file` (string): File where exception occurred
- `exception_line` (int): Line number where exception occurred
- `exception` (Throwable): Full exception object (Laravel automatically includes stack trace)

### Event-Specific Context
Additional context fields specific to the error type:
- `event_type` (string): Error category (e.g., `service_failure`, `save_retry`, `validation_error`, `deserialization_error`)
- Error-specific fields (e.g., `theme`, `flavor`, `accent`, `retry_count`, `invalid_value`)

## Error Alerting Requirements

### Severity Levels

1. **Critical** (requires immediate attention)
   - Service failures that prevent theme functionality
   - Database connection failures during theme save
   - Security-related errors (XSS attempts, SQL injection attempts)

2. **Error** (requires investigation)
   - Theme save failures after all retries exhausted
   - ThemeAccentMapper service failures
   - Deserialization failures that cannot be recovered

3. **Warning** (monitor for patterns)
   - Validation corrections (invalid theme combinations auto-corrected)
   - Save retries (temporary failures with retry mechanism)
   - Invalid theme values detected and corrected

4. **Info** (informational)
   - Theme changes (user-initiated)
   - Preview interactions
   - Successful validations

### Alert Conditions

**When to Alert:**
- Critical errors: Always alert immediately
- Error level: Alert if error rate exceeds 5 errors per minute
- Warning level: Alert if warning rate exceeds 20 warnings per minute
- Pattern detection: Alert if same error occurs 10+ times in 5 minutes

**Alert Channels:**
- Critical/Error: Email, Slack, PagerDuty (if configured)
- Warning: Slack channel (if configured)
- Info: No alerts (logged only)

**Deduplication:**
- Group similar errors by `exception_class`, `exception_file`, `exception_line`
- Deduplicate within 5-minute windows
- Include error count in alert message

## Error Rate Tracking

### Metrics to Track

1. **Error Frequency**
   - Errors per minute/hour/day
   - Error rate by type (`event_type`)
   - Error rate by user (anonymized)

2. **Error Types**
   - Service failures (`service_failure`)
   - Save retries (`save_retry`)
   - Validation errors (`validation_error`)
   - Deserialization errors (`deserialization_error`)

3. **Resolution Tracking**
   - Time to resolution (if tracked manually)
   - Error recurrence rate
   - Error patterns (same error recurring)

### Implementation

Error rate tracking can be implemented using:
- Laravel Telescope (for development/staging)
- External monitoring services (Sentry, Datadog, New Relic) for production
- Custom database logging for historical analysis

**Note**: Full error rate tracking implementation (T027f, FR-102) may require additional infrastructure setup.

## Example Error Log Entry

```json
{
  "message": "ThemeAccentMapper service failure",
  "timestamp": "2024-01-15T10:30:45Z",
  "timezone": "UTC",
  "environment": "production",
  "request_id": "req_abc123",
  "user_id": 42,
  "user_authenticated": true,
  "session_id": "session_xyz789",
  "request_url": "https://example.com/settings/appearance",
  "request_method": "POST",
  "request_ip": "192.168.1.1",
  "request_user_agent": "Mozilla/5.0...",
  "request_referer": "https://example.com/settings",
  "exception_class": "App\\Services\\Theme\\ThemeAccentMapperException",
  "exception_message": "Invalid accent for theme",
  "exception_code": 0,
  "exception_file": "/app/Services/Theme/ThemeAccentMapper.php",
  "exception_line": 45,
  "event_type": "service_failure",
  "theme": "kanagawa",
  "accent": "blue"
}
```

## Security Considerations

- **No sensitive data**: Passwords, API tokens, credit card numbers are never logged
- **PII exclusion**: Email addresses, phone numbers, personal information excluded
- **Data masking**: Payload data is logged as structure only, not content
- **GDPR compliance**: User IDs are logged but can be anonymized for compliance

## Related Tasks

- T027a [FR-036]: Telescope event recording
- T027b [FR-038]: Log level configuration
- T027f [FR-102]: Invalid theme combination tracking
- T027k [FR-037]: Telemetry anonymization
- T027l [FR-077]: Security audit logging
