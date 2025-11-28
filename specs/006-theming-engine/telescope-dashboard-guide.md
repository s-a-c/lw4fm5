# Telescope Dashboard Guide for Theme Events

**Task**: T027d [FR-099]
**Status**: Complete

## Overview

This guide explains how to view and analyze theme-related events in Laravel Telescope, including filtering, metrics, and dashboard configuration.

## Filtering Theme Events in Telescope

### Using Tags

Theme events are automatically tagged in Telescope for easy filtering:

- `theme:event` - All theme-related events
- `theme:theme_changed` - Theme change events
- `theme:validation_corrected` - Theme validation correction events
- `theme:preview_interaction` - Preview page interaction events
- `theme:performance` - Performance marker events
- `theme:error` - Theme-related errors

### Filtering by Event Type

1. Navigate to Telescope dashboard: `/telescope`
2. Go to the **Logs** tab
3. Use the search/filter to find entries with:
   - Tag: `theme:theme_changed`
   - Or search for: `event_type: theme_changed`

### Filtering by Event Type in Logs

In the Telescope Logs view, you can filter by:

- **Tag**: Select `theme:theme_changed` from the tag filter
- **Search**: Enter `theme_changed` in the search box
- **Level**: Filter by log level (info, warning, error)

## Dashboard Metrics

### Available Metrics

The following metrics are available for theme events:

#### Latency Percentiles
- **P50**: Median latency (50th percentile)
- **P95**: 95th percentile latency
- **P99**: 99th percentile latency
- **Max**: Maximum observed latency

#### Event Counts
- Total events by type
- Error counts
- Event frequency over time

#### Error Rates
- Overall error rate
- Error rate by event type
- Error rate trends

### Accessing Metrics via Artisan Command

Use the `theme:telescope-metrics` command to view aggregated metrics:

```bash
# Show aggregated metrics for all event types (last 24 hours)
php artisan theme:telescope-metrics --aggregated

# Show metrics for specific event type
php artisan theme:telescope-metrics --event-type=theme_changed

# Show metrics for last 48 hours
php artisan theme:telescope-metrics --hours=48

# Show performance metrics from ThemePerformanceTracker
php artisan theme:telescope-metrics --performance
```

### Example Output

```
Aggregated Theme Event Metrics
Time Range: Last 24 hours

Overall Metrics:
┌─────────────────────┬────────┐
│ Metric              │ Value  │
├─────────────────────┼────────┤
│ Total Events        │ 150    │
│ Total Errors        │ 2      │
│ Overall Error Rate  │ 1.33%  │
│ P50 Latency (ms)    │ 45.23  │
│ P95 Latency (ms)    │ 89.12  │
│ P99 Latency (ms)    │ 125.45 │
│ Max Latency (ms)    │ 198.76 │
└─────────────────────┴────────┘

Metrics by Event Type:
  theme_changed:
    Events: 100
    Errors: 1 (1.00%)
    P95 Latency: 85.23 ms
  validation_corrected:
    Events: 30
    Errors: 1 (3.33%)
    P95 Latency: 12.45 ms
  preview_interaction:
    Events: 20
    Errors: 0 (0.00%)
    P95 Latency: 15.67 ms
```

## Performance Metrics

Performance metrics are tracked separately using `ThemePerformanceTracker` and can be accessed via:

```bash
php artisan theme:telescope-metrics --performance
```

This displays:
- DOM update time percentiles
- Database query time percentiles
- Total time percentiles
- Sample counts for each metric

## Telescope UI Filtering

### Filtering by Event Type

1. Navigate to `/telescope/logs`
2. In the search box, enter: `event_type:theme_changed`
3. Or use the tag filter: `theme:theme_changed`

### Viewing Event Details

Click on any log entry to view:
- Full event context
- User ID (if authenticated)
- Session ID
- Request ID
- Performance metrics (if available)
- Timestamp and timezone

## Dashboard Configuration Recommendations

### Recommended Views

1. **Theme Events Overview**
   - Filter: `tag:theme:event`
   - Time Range: Last 24 hours
   - Group by: Event Type

2. **Theme Change Events**
   - Filter: `tag:theme:theme_changed`
   - Time Range: Last 7 days
   - Focus: Latency and error rates

3. **Validation Corrections**
   - Filter: `tag:theme:validation_corrected`
   - Time Range: Last 24 hours
   - Focus: Correction frequency and patterns

4. **Performance Monitoring**
   - Filter: `tag:theme:performance`
   - Time Range: Last 1 hour
   - Focus: P95 and P99 latencies

### Metrics to Monitor

- **P95 Latency**: Should be < 200ms for theme changes
- **Error Rate**: Should be < 1% for theme_changed events
- **Validation Correction Rate**: Monitor for patterns indicating data issues
- **Preview Interaction Rate**: Track user engagement with preview page

## Data Retention

Telescope data is automatically pruned after 7 days (configurable via `TELESCOPE_DB_RETENTION_DAYS`).

Performance metrics from `ThemePerformanceTracker` are retained for 24 hours in cache.

## API Access

For programmatic access to metrics, use the `ThemeTelescopeMetrics` service:

```php
use App\Services\Theme\ThemeTelescopeMetrics;

// Get metrics for specific event type
$metrics = ThemeTelescopeMetrics::getEventMetrics('theme_changed', 24);

// Get aggregated metrics
$aggregated = ThemeTelescopeMetrics::getAggregatedMetrics(24);

// Get performance metrics
$performance = ThemeTelescopeMetrics::getPerformanceMetrics('theme_save');
```

## Troubleshooting

### No Events Showing

1. Verify Telescope is enabled in your environment
2. Check that events are being tagged correctly (see `TelescopeServiceProvider`)
3. Verify log levels are configured correctly (see `config/logging.php`)

### Missing Performance Metrics

1. Ensure `ThemePerformanceTracker` is recording metrics
2. Check cache is working (metrics stored in cache)
3. Verify client-side performance tracking is sending data

### High Error Rates

1. Review error logs in Telescope
2. Check `ThemeErrorLogger` for structured error context
3. Review validation correction patterns
