# Observability Dashboards Configuration

**Task**: T027h [FR-105]
**Status**: Complete

## Overview

This document defines the observability dashboards needed for theme monitoring, metrics displayed, real-time vs. historical data, and access control.

## Required Dashboards

### 1. Theme Events Overview Dashboard

**Purpose**: High-level view of all theme-related events

**Metrics Displayed**:
- Total events (last 24 hours, 7 days, 30 days)
- Events by type (theme_changed, validation_corrected, preview_interaction)
- Error rate (percentage of events that are errors)
- Event frequency over time (line chart)

**Time Range**:
- Real-time: Last 1 hour
- Historical: Last 24 hours, 7 days, 30 days

**Access Control**:
- Development: All authenticated users
- Staging/Production: Admin users only (via Telescope gate)

**Location**: Telescope Logs tab with filter `tag:theme:event`

### 2. Theme Performance Dashboard

**Purpose**: Monitor theme operation performance

**Metrics Displayed**:
- P50, P95, P99, Max latencies for:
  - DOM update time
  - Database query time
  - Total time
- Performance by operation type (theme_save, theme_change, theme_preview)
- Performance trends over time

**Time Range**:
- Real-time: Last 1 hour
- Historical: Last 24 hours, 7 days

**Access Control**: Same as Theme Events Overview

**Location**:
- Telescope Logs tab with filter `tag:theme:performance`
- CLI: `php artisan theme:telescope-metrics --performance`

### 3. Theme Validation Dashboard

**Purpose**: Monitor theme validation corrections

**Metrics Displayed**:
- Validation correction frequency
- Most common invalid combinations
- Correction patterns over time
- Error rate for validation failures

**Time Range**:
- Real-time: Last 1 hour
- Historical: Last 24 hours, 7 days

**Access Control**: Same as Theme Events Overview

**Location**: Telescope Logs tab with filter `tag:theme:validation_corrected`

### 4. Security Audit Dashboard

**Purpose**: Monitor security events related to themes

**Metrics Displayed**:
- Failed validation attempts
- Rate limit violations
- Unauthorized access attempts
- Theme preference changes (user, timestamp, IP)

**Time Range**:
- Real-time: Last 1 hour
- Historical: Last 24 hours, 7 days, 30 days

**Access Control**:
- Development: All authenticated users
- Staging/Production: Security team only (restricted Telescope access)

**Location**: Telescope Logs tab with filter `tag:theme:error` or `event_type:security_audit`

### 5. Preview Page Analytics Dashboard

**Purpose**: Track preview page usage and conversions

**Metrics Displayed**:
- Preview page visits
- Theme popularity (most previewed themes)
- Session duration
- Interaction frequency
- Conversion rate (preview → signup)

**Time Range**:
- Real-time: Last 1 hour
- Historical: Last 24 hours, 7 days, 30 days

**Access Control**: Same as Theme Events Overview

**Location**: Telescope Logs tab with filter `tag:theme:preview_interaction`

## Real-Time vs. Historical Metrics

### Real-Time Metrics (Last 1 Hour)

- Current event rates
- Active performance metrics
- Recent security events
- Live preview interactions

**Update Frequency**: Every 30 seconds (Telescope auto-refresh)

### Historical Metrics (24 hours, 7 days, 30 days)

- Trend analysis
- Performance baselines
- Error rate trends
- Usage patterns

**Update Frequency**: On-demand (manual refresh)

## Dashboard Access Control

### Development Environment

- **Access**: All authenticated users
- **Authentication**: Laravel authentication required
- **Authorization**: No additional restrictions

### Staging Environment

- **Access**: Admin users only
- **Authentication**: Laravel authentication required
- **Authorization**: Via Telescope gate (`viewTelescope`)

### Production Environment

- **Access**: Admin users only
- **Authentication**: Laravel authentication required
- **Authorization**: Via Telescope gate (`viewTelescope`)
- **Additional Security**: IP whitelist (optional, via middleware)

### Telescope Gate Configuration

Current gate configuration in `TelescopeServiceProvider`:

```php
protected function gate(): void
{
    Gate::define('viewTelescope', fn (?User $user): bool => false);
}
```

**Recommendation**: Update to allow admin users:

```php
protected function gate(): void
{
    Gate::define('viewTelescope', function (?User $user): bool {
        if ($user === null) {
            return false;
        }

        // Allow in local environment
        if ($this->app->environment('local')) {
            return true;
        }

        // Restrict to admin users in other environments
        return $user->is_admin ?? false; // Adjust based on your admin check
    });
}
```

## Metrics Aggregation

### CLI Access

Use the `theme:telescope-metrics` command for programmatic access:

```bash
# Aggregated metrics
php artisan theme:telescope-metrics --aggregated

# Specific event type
php artisan theme:telescope-metrics --event-type=theme_changed

# Performance metrics
php artisan theme:telescope-metrics --performance

# Custom time range
php artisan theme:telescope-metrics --hours=48
```

### API Access (Future)

Consider creating API endpoints for dashboard integration:

- `/api/theme/metrics/events` - Event metrics
- `/api/theme/metrics/performance` - Performance metrics
- `/api/theme/metrics/security` - Security audit metrics

## Dashboard Recommendations

### Recommended Layout

1. **Top Row**: Key metrics (total events, error rate, P95 latency)
2. **Middle Row**: Event type breakdown (pie/bar chart)
3. **Bottom Row**: Time series charts (events over time, performance trends)

### Alert Integration

Dashboards should integrate with alerting system (see T027i for alert conditions).

### Export Capabilities

- CSV export for historical data
- JSON export for API integration
- PDF reports for compliance
