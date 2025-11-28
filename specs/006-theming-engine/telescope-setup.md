# Telescope Setup and Installation

**Task**: T027n [FR-108]
**Status**: Complete

## Installation

### Package Installation

Telescope is already installed via Composer:

```bash
composer require laravel/telescope
```

**Version**: `^5.15` (as per `composer.json`)

### Installation Steps

1. **Publish Configuration**:
   ```bash
   php artisan telescope:install
   ```

2. **Run Migrations**:
   ```bash
   php artisan migrate
   ```

3. **Configure Service Provider**:
   - Service provider: `App\Providers\TelescopeServiceProvider`
   - Already configured in `bootstrap/providers.php`

4. **Configure Authorization**:
   - Gate defined in `TelescopeServiceProvider::gate()`
   - Currently: `false` (no access in non-local environments)
   - **Recommendation**: Update to allow admin users (see below)

## Environment-Specific Configuration

### Development Environment

**Configuration**: Full observability enabled

- **Telescope Enabled**: Yes
- **Data Retention**: 7 days (configurable via `TELESCOPE_DB_RETENTION_DAYS`)
- **Access Control**: All authenticated users
- **Performance Impact**: Acceptable (< 5% overhead)

**Configuration File**: `config/telescope.php`

**Key Settings**:
```php
'enabled' => env('TELESCOPE_ENABLED', true),
'watchers' => [
    Watchers\LogWatcher::class => [
        'enabled' => true,
        'level' => 'debug', // Capture all logs
    ],
    // ... other watchers
],
```

### Staging Environment

**Configuration**: Selective observability

- **Telescope Enabled**: Yes
- **Data Retention**: 7 days
- **Access Control**: Admin users only
- **Performance Impact**: Acceptable (< 5% overhead)
- **Filtering**: Only record errors, failed jobs, and tagged events

**Key Settings**:
```php
'enabled' => env('TELESCOPE_ENABLED', true),
'watchers' => [
    Watchers\LogWatcher::class => [
        'enabled' => true,
        'level' => 'error', // Only errors and above
    ],
],
```

**Filtering** (in `TelescopeServiceProvider`):
- Record all data in local environment
- In staging: Only record errors, failed jobs, scheduled tasks, and tagged events
- Theme events are tagged, so they are recorded

### Production Environment

**Configuration**: Minimal observability

- **Telescope Enabled**: Yes (with filtering)
- **Data Retention**: 7 days
- **Access Control**: Admin users only (strict)
- **Performance Impact**: Minimal (< 2% overhead)
- **Filtering**: Only record errors, failed jobs, and tagged events

**Key Settings**:
```php
'enabled' => env('TELESCOPE_ENABLED', true),
'watchers' => [
    Watchers\LogWatcher::class => [
        'enabled' => true,
        'level' => 'error', // Only errors and above
    ],
],
```

**Security**:
- IP whitelist (optional, via middleware)
- Strict authentication
- HTTPS only

## Observability Feature Flags

### Environment Variable

```env
TELESCOPE_ENABLED=true
```

### Disabling Observability

**Development**: Set `TELESCOPE_ENABLED=false` (not recommended)

**Staging/Production**:
- Can be disabled via environment variable
- **Recommendation**: Keep enabled but restrict access
- Use filtering instead of disabling

### Feature Flag Implementation

Current implementation in `TelescopeServiceProvider`:

```php
Telescope::filter(function (IncomingEntry $entry) {
    if ($this->app->environment('local')) {
        return true; // Record everything in local
    }

    // In other environments, only record important events
    return $entry->isReportableException() ||
        $entry->isFailedJob() ||
        $entry->isScheduledTask() ||
        $entry->isSlowQuery() ||
        $entry->hasMonitoredTag(); // Theme events are tagged
});
```

## Performance Overhead Requirements

### Acceptable Impact

- **Development**: < 5% overhead acceptable
- **Staging**: < 3% overhead acceptable
- **Production**: < 2% overhead acceptable

### Performance Optimization

1. **Filtering**: Only record necessary events (already implemented)
2. **Data Retention**: Limit retention period (7 days)
3. **Pruning**: Automatic pruning via scheduled command
4. **Queue Processing**: Use queue for Telescope entries (optional)

### Monitoring Performance Impact

Monitor Telescope's impact via:
- Application response times
- Database query counts
- Memory usage
- CPU usage

### Queue Configuration (Optional)

To reduce performance impact, configure Telescope to use queues:

```php
// config/telescope.php
'queue' => [
    'connection' => env('TELESCOPE_QUEUE_CONNECTION'),
    'queue' => env('TELESCOPE_QUEUE', 'default'),
    'delay' => env('TELESCOPE_QUEUE_DELAY', 10),
],
```

**Current State**: Queues not configured (synchronous processing)

## Data Retention

### Current Configuration

- **Retention Period**: 7 days (configurable via `TELESCOPE_DB_RETENTION_DAYS`)
- **Pruning**: Automatic via scheduled command in `routes/console.php`

### Pruning Command

```php
// routes/console.php
Schedule::command('telescope:prune --hours='.env('TELESCOPE_DB_RETENTION_DAYS', 7) * 24)
    ->daily();
```

### Storage Considerations

- **Database**: Telescope entries stored in `telescope_entries` table
- **Size**: Monitor table size regularly
- **Cleanup**: Automatic pruning prevents unbounded growth

## Access Control Configuration

### Current Gate

```php
// TelescopeServiceProvider::gate()
Gate::define('viewTelescope', fn (?User $user): bool => false);
```

### Recommended Gate (Update Required)

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
        // Adjust based on your admin check implementation
        return $user->is_admin ?? false;
    });
}
```

## Configuration Summary

### Development

- ✅ Full observability
- ✅ All logs recorded
- ✅ All authenticated users can access
- ✅ 7-day retention

### Staging

- ✅ Selective observability
- ✅ Errors and tagged events only
- ✅ Admin users only
- ✅ 7-day retention

### Production

- ✅ Minimal observability
- ✅ Errors and tagged events only
- ✅ Admin users only (strict)
- ✅ 7-day retention
- ✅ Performance optimized

## Verification

### Verify Installation

```bash
# Check Telescope is installed
composer show laravel/telescope

# Check migrations
php artisan migrate:status | grep telescope

# Check service provider
php artisan package:discover | grep Telescope
```

### Verify Configuration

```bash
# Check configuration
php artisan config:show telescope

# Test access (in browser)
# Navigate to /telescope
```

### Verify Performance

```bash
# Monitor response times
# Compare with Telescope enabled vs disabled
```

## Troubleshooting

### Telescope Not Recording Events

1. Check `TELESCOPE_ENABLED` environment variable
2. Check filtering logic in `TelescopeServiceProvider`
3. Verify events are tagged correctly
4. Check database connection

### Performance Issues

1. Enable queue processing for Telescope
2. Increase data retention pruning frequency
3. Review filtering logic
4. Monitor database table size

### Access Issues

1. Check gate configuration
2. Verify user authentication
3. Check environment settings
4. Review middleware configuration
