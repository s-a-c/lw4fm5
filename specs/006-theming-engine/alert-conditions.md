# Alert Conditions Definition

**Task**: T027i [FR-106]
**Status**: Complete

## Overview

This document defines alert conditions for theme-related events, including thresholds, conditions, alert channels, severity levels, and deduplication strategies.

## Alert Conditions

### 1. High Error Rate Alert

**When to Alert**: Error rate exceeds threshold

**Threshold**:
- Warning: Error rate > 1% over 5 minutes
- Critical: Error rate > 5% over 5 minutes

**Conditions**:
- Calculate error rate: `(error_count / total_events) * 100`
- Monitor over rolling 5-minute window
- Alert if threshold exceeded for 2 consecutive windows

**Severity**:
- Warning: `warning`
- Critical: `critical`

**Alert Channel**:
- Warning: Slack/Email
- Critical: PagerDuty/On-call

**Deduplication**:
- Group by error type
- Max 1 alert per error type per 15 minutes
- Escalate to critical if warning persists > 30 minutes

### 2. Performance Degradation Alert

**When to Alert**: P95 latency exceeds target

**Threshold**:
- Warning: P95 latency > 200ms for 5 minutes
- Critical: P95 latency > 500ms for 5 minutes

**Conditions**:
- Monitor P95 latency for `theme_save` and `theme_change` operations
- Calculate over rolling 5-minute window
- Alert if threshold exceeded for 2 consecutive windows

**Severity**:
- Warning: `warning`
- Critical: `critical`

**Alert Channel**:
- Warning: Slack
- Critical: PagerDuty

**Deduplication**:
- Max 1 alert per operation type per 15 minutes
- Escalate to critical if warning persists > 30 minutes

### 3. High Validation Correction Rate Alert

**When to Alert**: Validation corrections exceed threshold

**Threshold**:
- Warning: > 10 corrections per hour
- Critical: > 50 corrections per hour

**Conditions**:
- Count `validation_corrected` events
- Monitor over rolling 1-hour window
- Alert if threshold exceeded

**Severity**:
- Warning: `warning`
- Critical: `critical`

**Alert Channel**:
- Warning: Slack
- Critical: Email to development team

**Deduplication**:
- Max 1 alert per hour
- Include most common invalid combinations in alert

### 4. Rate Limit Violation Spike Alert

**When to Alert**: Rate limit violations spike

**Threshold**:
- Warning: > 5 violations per 10 minutes
- Critical: > 20 violations per 10 minutes

**Conditions**:
- Count `rate_limit_violation` security audit events
- Monitor over rolling 10-minute window
- Alert if threshold exceeded

**Severity**:
- Warning: `warning`
- Critical: `critical`

**Alert Channel**:
- Warning: Slack
- Critical: PagerDuty (possible attack)

**Deduplication**:
- Max 1 alert per 15 minutes
- Include source IPs in alert

### 5. Preview Page Error Alert

**When to Alert**: Preview page errors exceed threshold

**Threshold**:
- Warning: Error rate > 2% for preview interactions
- Critical: Error rate > 10% for preview interactions

**Conditions**:
- Calculate error rate for `preview_interaction` events
- Monitor over rolling 5-minute window
- Alert if threshold exceeded

**Severity**:
- Warning: `warning`
- Critical: `critical`

**Alert Channel**:
- Warning: Slack
- Critical: Email to development team

**Deduplication**:
- Max 1 alert per 15 minutes

## Severity Levels

### Critical

- **Definition**: Immediate action required, system functionality at risk
- **Response Time**: < 15 minutes
- **Examples**:
  - P95 latency > 500ms
  - Error rate > 5%
  - Rate limit violations > 20/10min (possible attack)

### Warning

- **Definition**: Attention needed, but not immediately critical
- **Response Time**: < 1 hour
- **Examples**:
  - P95 latency > 200ms
  - Error rate > 1%
  - Validation corrections > 10/hour

### Info

- **Definition**: Informational, no action required
- **Response Time**: N/A
- **Examples**:
  - High preview page usage
  - Theme popularity trends

## Alert Channels

### Development Environment

- **Slack**: All alerts to development channel
- **Email**: Critical alerts only

### Staging Environment

- **Slack**: Warning and critical alerts
- **Email**: Critical alerts only

### Production Environment

- **Slack**: Warning alerts
- **PagerDuty**: Critical alerts (on-call rotation)
- **Email**: All alerts to security team

## Deduplication Strategy

### Time-Based Deduplication

- **Window**: 15 minutes for warnings, 5 minutes for critical
- **Method**: Suppress duplicate alerts within window
- **Escalation**: Escalate if alert persists beyond window

### Grouping Strategy

- **By Error Type**: Group similar errors together
- **By Operation**: Group by operation type (theme_save, theme_change, etc.)
- **By Source**: Group by source IP for security alerts

### Alert Summary

- **Format**: Include summary of all alerts in group
- **Frequency**: Send summary every 15 minutes if multiple alerts
- **Details**: Include count, first occurrence, last occurrence

## Alert Message Format

### Standard Format

```
[SEVERITY] Theme Alert: [Alert Type]

Threshold: [threshold]
Current Value: [current_value]
Time Window: [time_window]

Details:
- [detail 1]
- [detail 2]

View Dashboard: [dashboard_url]
```

### Example

```
[CRITICAL] Theme Alert: High Error Rate

Threshold: 5%
Current Value: 7.2%
Time Window: Last 5 minutes

Details:
- Total Events: 150
- Error Count: 11
- Most Common Error: ThemeAccentMapper service failure

View Dashboard: /telescope/logs?tag=theme:error
```

## Alert Testing

### Test Scenarios

1. **Error Rate Spike**: Simulate errors to trigger alert
2. **Performance Degradation**: Simulate slow operations
3. **Validation Corrections**: Create invalid theme combinations
4. **Rate Limit Violations**: Exceed rate limits

### Test Frequency

- Weekly: Test all alert conditions
- Monthly: Review and update thresholds
- Quarterly: Review alert effectiveness

## Threshold Tuning

### Initial Thresholds

Thresholds are set based on:
- Performance requirements (P95 < 200ms)
- Expected error rates (< 1%)
- Historical data analysis

### Adjustment Process

1. Monitor alerts for 2 weeks
2. Analyze false positive rate
3. Adjust thresholds based on:
   - False positive rate
   - Actual incident frequency
   - Business impact

### Review Schedule

- **Weekly**: Review alert frequency
- **Monthly**: Review threshold effectiveness
- **Quarterly**: Comprehensive threshold review
