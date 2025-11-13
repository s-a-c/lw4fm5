# Spatie Laravel Packages Configuration

Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 -->

## 1 Introduction

This document covers all Spatie Laravel packages used in the project, including their configuration and integration.

> [!NOTE]
> All Spatie packages follow Laravel conventions and use auto-discovery. For detailed documentation, see the official Spatie package documentation at [spatie.be/open-source](https://spatie.be/open-source) or individual package repositories on GitHub.

## 2 Package List

The project uses the following Spatie packages:

- `spatie/laravel-activitylog` ^4.10 - Activity logging
- `spatie/laravel-analytics` ^5.6 - Google Analytics integration
- `spatie/laravel-backup` ^9.3 - Database and file backups
- `spatie/laravel-event-sourcing` ^7.12 - Event sourcing implementation
- `spatie/laravel-health` ^1.34 - Health check endpoints
- `spatie/laravel-markdown` ^2.7 - Markdown rendering
- `spatie/laravel-medialibrary` ^11.17 - File and media management
- `spatie/laravel-schedule-monitor` ^4.1 - Scheduled task monitoring
- `spatie/laravel-settings` ^3.5 - Application settings management

## 3 Activity Log

### 3.1 Package Overview

**Package**: `spatie/laravel-activitylog` ^4.10
**Purpose**: Log activity changes to your models
**Key Features**:
\* Automatic logging of model changes
\* Custom activity logging
\* Log viewer and search
\* Activity descriptions and properties

### 3.2 Configuration

``` bash
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate
```

### 3.3 Usage Examples

#### 3.3.1 Basic Logging

``` php
use Spatie\Activitylog\Models\Activity;

// Log a simple message
activity()->log('User logged in');

// Log with properties
activity()
    ->withProperties(['ip' => request()->ip()])
    ->log('User logged in');
```

#### 3.3.2 Model Activity Logging

Add the `LogsActivity` trait to your model:

``` php
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
```

#### 3.3.3 Retrieving Activity Logs

``` php
// Get all activities for a model
$user->activities;

// Get activities for a specific model
Activity::forSubject($user)->get();

// Get activities by description
Activity::where('description', 'updated')->get();
```

## 4 Analytics

### 4.1 Package Overview

**Package**: `spatie/laravel-analytics` ^5.6
**Purpose**: Retrieve data from Google Analytics
**Key Features**:
\* Fetch analytics data
\* Real-time reporting
\* Custom date ranges
\* Multiple metrics and dimensions

### 4.2 Configuration

Requires Google Analytics credentials. Set in `.env`:

``` env
ANALYTICS_PROPERTY_ID=your-property-id
ANALYTICS_CREDENTIALS=/path/to/credentials.json
```

### 4.3 Usage Examples

#### 4.3.1 Basic Analytics Query

``` php
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

// Get visitors and page views for the past 7 days
$analyticsData = Analytics::fetchVisitorsAndPageViews(Period::days(7));

// Get most visited pages
$mostVisitedPages = Analytics::fetchMostVisitedPages(Period::days(30));

// Get top referrers
$topReferrers = Analytics::fetchTopReferrers(Period::days(30));
```

#### 4.3.2 Custom Queries

``` php
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

$response = Analytics::get(
    Period::days(7),
    ['activeUsers', 'screenPageViews'],
    ['pageTitle', 'pagePath']
);
```

## 5 Backup

### 5.1 Package Overview

**Package**: `spatie/laravel-backup` ^9.3
**Purpose**: Backup your application and database
**Key Features**:
\* Database backups
\* File backups
\* Multiple storage destinations
\* Backup scheduling
\* Cleanup of old backups

### 5.2 Configuration

``` bash
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

Configure backup destinations in `config/backup.php`.

### 5.3 Usage Examples

#### 5.3.1 Manual Backup

``` bash
# Create a backup
php artisan backup:run

# Backup only database
php artisan backup:run --only-db

# Backup only files
php artisan backup:run --only-files
```

#### 5.3.2 Scheduled Backups

Add to `app/Console/Kernel.php` or use task scheduling:

``` php
use Illuminate\Support\Facades\Schedule;

Schedule::command('backup:run')->daily();
Schedule::command('backup:clean')->daily();
```

#### 5.3.3 Programmatic Backup

``` php
use Spatie\Backup\Tasks\Backup\BackupJob;

$backupJob = new BackupJob();
$backupJob->run();
```

## 6 Event Sourcing

### 6.1 Package Overview

**Package**: `spatie/laravel-event-sourcing` ^7.12
**Purpose**: Event sourcing implementation for Laravel
**Key Features**:
\* Event store
\* Aggregate roots
\* Projectors and reactors
\* Snapshots
\* Event replay

### 6.2 Configuration

``` bash
php artisan vendor:publish --provider="Spatie\EventSourcing\EventSourcingServiceProvider"
php artisan migrate
```

### 6.3 Usage Examples

#### 6.3.1 Creating Events

``` php
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class MoneyAdded extends ShouldBeStored
{
    public function __construct(
        public int $amount
    ) {}
}
```

#### 6.3.2 Aggregate Root

``` php
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class Account extends AggregateRoot
{
    private int $balance = 0;

    public function addMoney(int $amount): self
    {
        $this->recordThat(new MoneyAdded($amount));

        return $this;
    }

    protected function applyMoneyAdded(MoneyAdded $event): void
    {
        $this->balance += $event->amount;
    }
}
```

#### 6.3.3 Using Aggregates

``` php
$account = Account::retrieve($accountId);
$account->addMoney(100);
$account->persist();
```

## 7 Health

### 7.1 Package Overview

**Package**: `spatie/laravel-health` ^1.34
**Purpose**: Health check endpoints for your application
**Key Features**:
\* Multiple health checks
\* Database connectivity
\* Cache status
\* Queue status
\* Custom checks

### 7.2 Configuration

Health checks are automatically available at `/health` endpoint.

### 7.3 Usage Examples

#### 7.3.1 Default Health Checks

Access the health endpoint:

``` bash
curl http://localhost/health
```

#### 7.3.2 Custom Health Checks

``` php
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

Check::make('custom-check')
    ->run(function () {
        // Your health check logic
        return Result::make()
            ->ok()
            ->shortSummary('All good');
    });
```

#### 7.3.3 Available Checks

``` php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\QueueCheck;

Health::checks([DatabaseCheck::new(),
    CacheCheck::new(),
    QueueCheck::new(),
]);
```

## 8 Markdown

### 8.1 Package Overview

**Package**: `spatie/laravel-markdown` ^2.7
**Purpose**: Render Markdown to HTML
**Key Features**:
\* Markdown rendering
\* Code highlighting
\* Table of contents
\* Custom extensions

### 8.2 Usage Examples

#### 8.2.1 Basic Usage

``` php
use Spatie\LaravelMarkdown\MarkdownRenderer;

$html = app(MarkdownRenderer::class)->toHtml($markdown);
```

#### 8.2.2 With Blade Directive

``` blade
@markdown
# Hello World

This is **bold** and this is *italic*.
@endmarkdown
```

#### 8.2.3 With Code Highlighting

``` php
use Spatie\LaravelMarkdown\MarkdownRenderer;

$renderer = app(MarkdownRenderer::class)
    ->highlightCode();

$html = $renderer->toHtml($markdown);
```

#### 8.2.4 Generating Table of Contents

``` php
use Spatie\LaravelMarkdown\MarkdownRenderer;

$renderer = app(MarkdownRenderer::class)
    ->addTableOfContents();

$html = $renderer->toHtml($markdown);
```

## 9 Media Library

### 9.1 Package Overview

**Package**: `spatie/laravel-medialibrary` ^11.17
**Purpose**: Associate files with Eloquent models
**Key Features**:
\* File uploads
\* Image conversions
\* Multiple collections
\* Responsive images
\* URL generation

### 9.2 Configuration

``` bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
php artisan migrate
```

### 9.3 Usage Examples

#### 9.3.1 Adding Files to Models

Add the `HasMedia` trait to your model:

``` php
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use InteractsWithMedia;

    // ...
}
```

#### 9.3.2 Uploading Files

``` php
$post = Post::find(1);

// Add file
$post->addMediaFromRequest('photo')
    ->toMediaCollection('images');

// Add from URL
$post->addMediaFromUrl('https://example.com/image.jpg')
    ->toMediaCollection('images');

// Add from disk
$post->addMediaFromDisk('path/to/file.jpg', 'local')
    ->toMediaCollection('images');
```

#### 9.3.3 Image Conversions

``` php
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

public function registerMediaConversions(?Media $media = null): void
{
    $this->addMediaConversion('thumb')
        ->width(150)
        ->height(150)
        ->fit(Fit::Crop, 150, 150);
}
```

#### 9.3.4 Retrieving Media

``` php
// Get first media item
$media = $post->getFirstMedia('images');

// Get media URL
$url = $post->getFirstMediaUrl('images');

// Get all media
$allMedia = $post->getMedia('images');
```

## 10 Schedule Monitor

### 10.1 Package Overview

**Package**: `spatie/laravel-schedule-monitor` ^4.1
**Purpose**: Monitor Laravel scheduled tasks
**Key Features**:
\* Automatic task monitoring
\* Task execution tracking
\* Failure notifications
\* Execution time tracking

### 10.2 Configuration

Automatically monitors scheduled tasks. No additional configuration required.

### 10.3 Usage Examples

#### 10.3.1 Automatic Monitoring

The package automatically monitors all scheduled tasks defined in your `app/Console/Kernel.php` or `routes/console.php`.

#### 10.3.2 Checking Task Status

``` php
use Spatie\ScheduleMonitor\Models\MonitoredScheduledTask;

// Get all monitored tasks
$tasks = MonitoredScheduledTask::all();

// Check if task is healthy
$task = MonitoredScheduledTask::where('name', 'your-command')->first();
if ($task->isHealthy()) {
    // Task is running as expected
}
```

#### 10.3.3 Task Statistics

``` php
$task = MonitoredScheduledTask::where('name', 'your-command')->first();

// Get last run time
$lastRun = $task->last_started_at;

// Get average execution time
$avgTime = $task->average_execution_time_in_seconds;
```

## 11 Settings

### 11.1 Package Overview

**Package**: `spatie/laravel-settings` ^3.5
**Purpose**: Manage application settings
**Key Features**:
\* Type-safe settings
\* Settings groups
\* Caching
\* Validation
\* Default values

### 11.2 Configuration

``` bash
php artisan vendor:publish --provider="Spatie\LaravelSettings\LaravelSettingsServiceProvider" --tag="migrations"
php artisan migrate
```

### 11.3 Usage Examples

#### 11.3.1 Creating Settings Class

``` php
use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;
    public string $site_email;
    public bool $site_active;

    public static function group(): string
    {
        return 'general';
    }
}
```

#### 11.3.2 Using Settings

``` php
use App\Settings\GeneralSettings;

// Get settings
$settings = app(GeneralSettings::class);
$siteName = $settings->site_name;

// Update settings
$settings->site_name = 'New Site Name';
$settings->save();

// Or use the facade
use Spatie\LaravelSettings\Facades\Settings;

Settings::group(GeneralSettings::class)->site_name = 'New Name';
Settings::group(GeneralSettings::class)->save();
```

#### 11.3.3 Settings with Defaults

``` php
use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name = 'Laravel App';
    public string $site_email = 'admin@example.com';
    public bool $site_active = true;

    public static function group(): string
    {
        return 'general';
    }
}
```

## 12 Next Steps

[Search & Analytics →](110-search-analytics.md)

## 13 Navigation

[← Telescope, Activity Log, and Health Checks](090-observability.md) | [↑ Top](#spatie-laravel-packages-configuration) | [Scout, Typesense, and Analytics Setup →](110-search-analytics.md)
