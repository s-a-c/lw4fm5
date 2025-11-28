# Horizon Configuration for Theme Operations

**Task**: T027m [FR-100]
**Status**: Complete

## Overview

This document defines Horizon configuration requirements for theme operations, including when Horizon should be configured, what queue metrics are relevant, and dashboard setup requirements.

## When Horizon Should Be Configured

### Current Implementation

**Theme operations do NOT use queues**. All theme operations are:
- Synchronous database writes (via `DB::transaction`)
- Immediate DOM updates (via Livewire)
- Real-time user feedback

**Conclusion**: Horizon is **NOT required** for theme operations in the current implementation.

### Future Considerations

If theme operations are moved to queues in the future, Horizon should be configured for:
- Theme preference change jobs
- Bulk theme updates
- Theme validation jobs
- Performance metric aggregation jobs

## Queue Metrics Relevant for Theming

### If Queues Are Used

The following queue metrics would be relevant:

1. **Job Throughput**
   - Jobs processed per minute
   - Jobs per hour
   - Peak throughput

2. **Job Latency**
   - Average job processing time
   - P95, P99 job processing time
   - Queue wait time

3. **Job Failure Rate**
   - Failed jobs per hour
   - Failure rate percentage
   - Most common failure reasons

4. **Queue Depth**
   - Pending jobs count
   - Queue backlog
   - Queue saturation

### Current State

Since theme operations are synchronous, these metrics are not applicable.

## Horizon Dashboard Setup Requirements

### If Queues Are Used

**Required Dashboards**:

1. **Theme Operations Queue Dashboard**
   - Queue name: `theme-operations` (hypothetical)
   - Metrics: Throughput, latency, failure rate
   - Filters: By job type (theme_save, theme_validate, etc.)

2. **Theme Performance Queue Dashboard**
   - Queue name: `theme-performance` (hypothetical)
   - Metrics: Aggregation job metrics
   - Filters: By operation type

**Metrics Displayed**:
- Jobs processed
- Jobs failed
- Average wait time
- Average processing time
- Throughput (jobs/minute)

**Access Control**: Same as Telescope (admin users only in staging/production)

### Current State

No Horizon dashboards are required since queues are not used.

## Handling Horizon When No Queues Are Used

### Is Horizon Optional?

**Yes, Horizon is optional** for theme operations since:
- Theme operations are synchronous
- No queue jobs are dispatched
- No queue monitoring is needed

### Should Horizon Be Disabled?

**No, Horizon should NOT be disabled** because:
- Other parts of the application may use queues
- Horizon provides valuable queue monitoring for the entire application
- Horizon has minimal overhead when no jobs are queued

### Configuration Recommendation

**Keep Horizon enabled** but understand that:
- Theme operations will not appear in Horizon
- Horizon metrics are not relevant for theme operations
- Telescope is the primary observability tool for themes

## Horizon Configuration (If Needed in Future)

### Basic Configuration

If queues are added for theme operations:

```php
// config/horizon.php
'environments' => [
    'production' => [
        'theme-operations' => [
            'connection' => 'redis',
            'queue' => 'theme-operations',
            'balance' => 'simple',
            'processes' => 3,
            'tries' => 3,
            'timeout' => 60,
        ],
    ],
],
```

### Monitoring Configuration

```php
// Monitor theme queue specifically
'waits' => [
    'theme-operations:default' => 60,
],
```

## Alternative: Direct Database Monitoring

Since theme operations are synchronous, monitor via:

1. **Telescope**: Request/response monitoring
2. **Database Query Watcher**: Query performance
3. **Performance Tracker**: Custom performance metrics
4. **Security Audit Logger**: Security events

## Summary

- **Horizon Required**: No (theme operations are synchronous)
- **Horizon Optional**: Yes (for other application queues)
- **Horizon Disabled**: No (keep enabled for other queues)
- **Queue Metrics**: Not applicable (no queues used)
- **Dashboard Setup**: Not required (no theme queues)

**Primary Observability Tool**: Telescope (not Horizon)
