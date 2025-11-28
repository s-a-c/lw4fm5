# Performance Monitoring Implementation

**Task**: T028o [FR-117]
**Status**: Complete

## Overview

This document defines the performance monitoring implementation for the theming engine, including tools, metrics, collection frequency, storage, retention, dashboards, and alerting.

## Monitoring Tools

### Primary Tools

1. **Laravel Telescope**:
   - Request/response monitoring
   - Query performance
   - Log aggregation
   - Event tracking

2. **ThemePerformanceTracker**:
   - Custom performance metrics
   - Percentile calculations
   - Operation-specific tracking

3. **ThemeTelescopeMetrics**:
   - Telescope data aggregation
   - Event metrics
   - Error rate tracking

### Secondary Tools

1. **Browser Performance API**:
   - Client-side DOM update time
   - Navigation timing
   - Resource timing

2. **Laravel Logs**:
   - Error tracking
   - Performance markers
   - Debug information

## Metrics Collected

### Performance Metrics

1. **DOM Update Time**:
   - Measurement: Client-side (Performance API)
   - Collection: Every theme change
   - Storage: Cache + Telescope

2. **Database Query Time**:
   - Measurement: Server-side (DB::listen)
   - Collection: Every theme save
   - Storage: Cache + Telescope

3. **Total Time**:
   - Measurement: Server-side (microtime)
   - Collection: Every theme operation
   - Storage: Cache + Telescope

### Event Metrics

1. **Event Counts**:
   - `theme_changed`: Theme preference changes
   - `validation_corrected`: Validation corrections
   - `preview_interaction`: Preview page interactions

2. **Error Rates**:
   - Error count per event type
   - Error rate percentage
   - Error trends over time

3. **Latencies**:
   - P50, P95, P99, Max percentiles
   - Per event type
   - Overall latencies

## Collection Frequency

### Real-Time Collection

**Frequency**: Every operation

**Metrics**:
- DOM update time (client-side)
- Database query time (server-side)
- Total time (server-side)
- Event counts

**Storage**: Immediate (Cache + Telescope)

### Aggregated Collection

**Frequency**: On-demand (CLI command)

**Metrics**:
- Percentiles (P50, P95, P99, Max)
- Error rates
- Event type breakdowns

**Storage**: Calculated from Telescope data

## Data Collection and Storage

### Storage Locations

1. **Laravel Cache**:
   - Performance samples (1000 per operation)
   - Correction frequency
   - Server-side correlation data
   - Retention: 24 hours (performance), 5 minutes (correlation)

2. **Telescope Database**:
   - Log entries with performance data
   - Event tracking
   - Error logs
   - Retention: 7 days (configurable)

3. **Laravel Logs**:
   - Error logs
   - Performance markers
   - Debug information
   - Retention: 14 days (configurable)

### Data Retention Policies

**Performance Samples**: 24 hours
**Correlation Data**: 5 minutes
**Telescope Entries**: 7 days
**Laravel Logs**: 14 days

**Rationale**:
- Performance samples: Recent data sufficient for percentiles
- Correlation data: Short-term only (client-server matching)
- Telescope: Balance between history and storage
- Logs: Longer retention for debugging

## Performance Dashboards

### Dashboard 1: Real-Time Performance

**Metrics**:
- Current P95 latency
- Current error rate
- Recent events (last hour)

**Update Frequency**: Every 30 seconds

**Location**: Telescope Logs tab (filter: `tag:theme:performance`)

### Dashboard 2: Historical Performance

**Metrics**:
- P50, P95, P99, Max latencies (24 hours, 7 days)
- Error rate trends
- Event frequency trends

**Update Frequency**: On-demand

**Location**: CLI command `php artisan theme:telescope-metrics --performance`

### Dashboard 3: Event Overview

**Metrics**:
- Total events by type
- Error counts
- Event frequency

**Update Frequency**: On-demand

**Location**: Telescope Logs tab (filter: `tag:theme:event`)

## Performance Alerting

### Alert Conditions

**See**: `specs/006-theming-engine/alert-conditions.md` for detailed alert conditions

**Summary**:
- P95 latency > 200ms (warning)
- P95 latency > 500ms (critical)
- Error rate > 1% (warning)
- Error rate > 5% (critical)

### Alert Channels

**Development**: Slack
**Staging**: Slack + Email
**Production**: Slack + PagerDuty

### Alert Frequency

**Deduplication**: 15 minutes (warnings), 5 minutes (critical)

**Escalation**: Warning → Critical after 30 minutes

## Monitoring Implementation

### Client-Side Monitoring

**Location**: `resources/js/app.js`

**Implementation**:
```javascript
const domUpdateStartTime = performance.now();
applyThemeToDom({ theme, flavor, accent });
const domUpdateEndTime = performance.now();
const domUpdateTime = domUpdateEndTime - domUpdateStartTime;

sendPerformanceMetrics('theme_change', domUpdateTime, correlationId);
```

### Server-Side Monitoring

**Location**: `app/Livewire/Settings/Appearance.php`

**Implementation**:
```php
$startTime = microtime(true);
// ... database operations ...
$totalTime = (microtime(true) - $startTime) * 1000;

ThemePerformanceTracker::record(
    operation: 'theme_save',
    domUpdateTime: $domUpdateTime,
    databaseQueryTime: $databaseQueryTime,
    totalTime: $totalTime,
);
```

## Monitoring Frequency

### Real-Time Monitoring

**Frequency**: Continuous

**Metrics**: All performance metrics

**Tools**: Telescope, Performance Tracker

### Historical Analysis

**Frequency**: Daily, Weekly, Monthly

**Metrics**: Aggregated metrics, trends

**Tools**: CLI commands, Telescope queries

## Conclusion

✅ **Performance monitoring implemented**

- Tools defined (Telescope, Performance Tracker)
- Metrics collected (DOM time, DB time, total time)
- Collection frequency defined (real-time + aggregated)
- Storage locations defined (Cache, Telescope, Logs)
- Retention policies defined (24h, 7d, 14d)
- Dashboards defined (real-time, historical, events)
- Alerting configured (thresholds, channels, frequency)

## Recommendations

1. **Regular Review**: Review performance metrics weekly
2. **Trend Analysis**: Track performance trends over time
3. **Alert Tuning**: Adjust alert thresholds based on data
4. **Dashboard Updates**: Update dashboards as needed
