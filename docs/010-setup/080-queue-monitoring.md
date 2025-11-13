# Horizon and Octane Queue Management

Compliant with [AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 -->

## 1 Introduction

This document covers Laravel Horizon for queue monitoring and Laravel Octane for high-performance application serving.

## 2 Laravel Horizon

### 2.1 Package Overview

**Package Name**: `laravel/horizon`
**Version**: `^5.38`
**Purpose**: Dashboard and monitoring for Laravel Redis queues
**Architectural Role**: Provides real-time monitoring and management of queue workers

**What is Laravel Horizon?**
Laravel Horizon is a beautiful dashboard and configuration system for Laravel’s Redis-powered queue. It provides real-time monitoring of your queue jobs, workers, and job metrics, making it easy to see what’s happening with your background jobs.

**Why use Horizon?**
\* **Real-time Monitoring**: See job status, throughput, and performance in real-time
\* **Beautiful Dashboard**: Easy-to-use web interface for monitoring queues
\* **Job Management**: Retry failed jobs, delete jobs, view job details
\* **Performance Metrics**: Track job throughput, wait times, and processing times
\* **Worker Management**: Monitor and manage queue workers
\* **Tag-based Filtering**: Organize and filter jobs by tags

**What are Queues?**
Queues allow you to defer time-consuming tasks (like sending emails, processing images, generating reports) to be processed in the background instead of making users wait. Horizon helps you monitor and manage these background jobs.

**Real-World Examples:**
\* **Email Sending**: Queue emails to be sent in the background instead of blocking the request
\* **Image Processing**: Resize or optimize images in the background
\* **Report Generation**: Generate reports asynchronously
\* **Data Import/Export**: Process large data imports/exports in the background
\* **Notifications**: Send push notifications without blocking the main request

### 2.2 Key Features

- **Real-time Dashboard**: Beautiful web interface for monitoring queues
- **Job Metrics**: Track throughput, wait times, and processing times
- **Failed Job Management**: View, retry, or delete failed jobs
- **Worker Monitoring**: See which workers are running and their status
- **Queue Configuration**: Configure queue workers and their settings
- **Tag-based Filtering**: Organize jobs with tags for easy filtering
- **Job Details**: View detailed information about each job
- **Historical Data**: View past job execution data

**Source**: [Laravel Horizon Documentation](https://laravel.com/docs/12.x/horizon)

### 2.3 Installation Using Artisan Command

Use the `php artisan horizon:install` command to install Horizon. This sets up the configuration and assets needed for the dashboard.

**Installation Command:**

``` bash
php artisan horizon:install
```

**What Happens During Installation:**

1. **Publishes Configuration**: Creates `config/horizon.php` configuration file
2. **Publishes Assets**: Sets up CSS and JavaScript assets for the dashboard
3. **Creates Migration**: Creates migration for Horizon’s monitoring tables
4. **Registers Routes**: Sets up routes for the Horizon dashboard (typically at `/horizon`)

**Expected Output:**

``` text
  INFO  Publishing Horizon configuration file...

  INFO  Publishing Horizon assets...

  INFO  Publishing Horizon migrations...

  INFO  Horizon installed successfully.

  INFO  Horizon dashboard available at: http://localhost/horizon
```

### 2.4 What Gets Installed

#### 2.4.1 Files Created

- `config/horizon.php` - Horizon configuration file
- `database/migrations/YYYY_MM_DD_HHMMSS_create_jobs_table.php` - Jobs table migration (if not exists)
- `database/migrations/YYYY_MM_DD_HHMMSS_create_failed_jobs_table.php` - Failed jobs table migration (if not exists)
- Assets in `public/vendor/horizon/` - Dashboard CSS and JavaScript

#### 2.4.2 Configuration Updates

- Routes automatically registered at `/horizon` (configurable)
- Horizon service provider automatically registered

#### 2.4.3 Database Requirements

- Redis server (required for queues)
- `jobs` table (stores queued jobs)
- `failed_jobs` table (stores failed jobs)

### 2.5 Configuration Steps

| Step \# | Task/Configuration Item | Command/File/Code | Expected Result | Verification Method |
|----|----|----|----|----|
| 1 | Verify installation | `composer show laravel/horizon` | Package version displayed | ^5.38 shown |
| 2 | Install Horizon | `php artisan horizon:install` | Configuration, assets, and migrations published | `config/horizon.php` exists |
| 3 | Install and start Redis | Install Redis and ensure it’s running | Redis server running | `redis-cli ping` returns PONG |
| 4 | Configure queue connection | Edit `.env` → set `QUEUE_CONNECTION=redis` | Queue connection set to Redis | Check `.env` file |
| 5 | Run migrations | `php artisan migrate` | Jobs and failed_jobs tables created | Tables exist in database |
| 6 | Start Horizon | `php artisan horizon` | Horizon dashboard accessible | Visit `/horizon` in browser |
| 7 | Configure supervisor (production) | Set up supervisor config for Horizon | Horizon runs as daemon in production | `supervisorctl status` shows horizon running |

### 2.6 Starting Horizon

**Development:**

Start Horizon in the foreground (you’ll see logs in the terminal):

``` bash
php artisan horizon
```

**Production:**

Use a process manager like Supervisor to keep Horizon running:

``` ini
[program:horizon]
process_name=%(program_name)s
command=php /path/to/your/app/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/horizon.log
stopwaitsecs=3600
```

### 2.7 Accessing the Horizon Dashboard

Once Horizon is running, access the dashboard at:

``` text
http://localhost/horizon
```

**Dashboard Features:**

- **Metrics**: See job throughput, wait times, and processing times
- **Jobs**: View all jobs (pending, processing, completed, failed)
- **Workers**: See running workers and their status
- **Failed Jobs**: View and retry failed jobs
- **Tags**: Filter jobs by tags
- **Search**: Search for specific jobs

## 3 Laravel Octane

### 3.1 Package Overview

**Package Name**: `laravel/octane`
**Version**: `^2.13`
**Purpose**: High-performance application server for Laravel
**Architectural Role**: Runs Laravel applications on high-performance servers (RoadRunner or Swoole)

**What is Laravel Octane?**
Laravel Octane is a high-performance application server for Laravel that uses RoadRunner or Swoole to serve your application. Unlike traditional PHP-FPM, Octane keeps your application loaded in memory between requests, resulting in significantly better performance.

**Why use Octane?**
\* **Better Performance**: 2-10x faster than traditional PHP-FPM
\* **Concurrent Requests**: Handle multiple requests simultaneously
\* **Lower Memory Usage**: More efficient memory management
\* **WebSocket Support**: Built-in WebSocket support with Swoole
\* **Hot Reloading**: Automatic code reloading during development
\* **Production Ready**: Used by many high-traffic Laravel applications

**How Octane Works:**

1. Traditional PHP-FPM: Each request loads the entire application, processes it, then unloads everything
2. Octane: Application is loaded once into memory and stays there, handling multiple requests quickly

**When to use Octane:**
\* **High-Traffic Applications**: Applications with many concurrent users
\* **API Endpoints**: Fast API responses
\* **Real-time Applications**: WebSocket support for real-time features
\* **Performance-Critical Applications**: When speed is important

**When NOT to use Octane:**
\* **Simple Applications**: Small applications may not need the extra complexity
\* **Development**: Standard PHP-FPM or `php artisan serve` is usually sufficient
\* **Shared Hosting**: Octane requires server-level access

### 3.2 Key Features

- **RoadRunner Support**: High-performance application server written in Go
- **Swoole Support**: PHP extension for high-performance networking
- **Application State Management**: Keeps application in memory between requests
- **Concurrent Request Handling**: Handle multiple requests simultaneously
- **Hot Reloading**: Automatically reload code changes during development
- **WebSocket Support**: Built-in WebSocket server (Swoole only)
- **Task Scheduling**: Built-in task scheduling
- **Process Management**: Automatic worker process management

**Source**: [Laravel Octane Documentation](https://laravel.com/docs/12.x/octane)

### 3.3 Installation Using Artisan Command

Use the `php artisan octane:install` command to install Octane. This sets up the configuration and helps you choose between RoadRunner and Swoole.

**Installation Command:**

``` bash
php artisan octane:install
```

**What Happens During Installation:**

1. **Prompts for Server Choice**: You’ll be asked to choose RoadRunner or Swoole
2. **Publishes Configuration**: Creates `config/octane.php` configuration file
3. **Installs Server**: Downloads and installs the chosen server (RoadRunner binary or Swoole extension)
4. **Creates Service Provider**: Sets up Octane service provider

**Expected Output:**

``` text
Which application server you would like to use?
  [0] RoadRunner
  [1] Swoole
 > 0

  INFO  Installing RoadRunner...

  INFO  Publishing Octane configuration file...

  INFO  Octane installed successfully.
```

### 3.4 Choosing a Server

**RoadRunner (Recommended for most cases):**
\* Written in Go (fast and efficient)
\* Easy to install (single binary)
\* Good for most applications
\* No PHP extension required

**Swoole:**
\* PHP extension (written in C)
\* More features (WebSockets, coroutines)
\* Better for real-time applications
\* Requires PHP extension installation

### 3.5 What Gets Installed

#### 3.5.1 Files Created

- `config/octane.php` - Octane configuration file
- `routes/octane.php` - Octane-specific routes (if needed)
- RoadRunner binary or Swoole extension installed

#### 3.5.2 Configuration Updates

- Octane service provider registered
- Configuration for chosen server set up

### 3.6 Configuration Steps

| Step \# | Task/Configuration Item | Command/File/Code | Expected Result | Verification Method |
|----|----|----|----|----|
| 1 | Verify installation | `composer show laravel/octane` | Package version displayed | ^2.13 shown |
| 2 | Install Octane | `php artisan octane:install` | Configuration created, server chosen | `config/octane.php` exists |
| 3 | Choose server | Select RoadRunner (0) or Swoole (1) | Server installed and configured | Server binary/extension available |
| 4 | Start Octane (development) | `php artisan octane:start` | Application running on Octane | Application accessible on configured port |
| 5 | Start Octane with reload (development) | `php artisan octane:start --watch` | Application running with hot reload | Code changes automatically reload |
| 6 | Configure for production | Set up process manager (Supervisor, systemd) | Octane runs as service | Service status shows running |

### 3.7 Starting Octane

**Development (with hot reload):**

``` bash
php artisan octane:start --watch
```

This starts Octane and automatically reloads when you change files.

**Development (without hot reload):**

``` bash
php artisan octane:start
```

**Production:**

Use a process manager like Supervisor:

``` ini
[program:octane]
process_name=%(program_name)s
command=php /path/to/your/app/artisan octane:start --server=roadrunner --host=0.0.0.0 --port=8000
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/octane.log
```

### 3.8 Important Considerations

**State Management:**
\* Octane keeps your application in memory
\* Static properties and singletons persist between requests
\* Be careful with global state
\* Use Octane’s state management features when needed

**Database Connections:**
\* Connection pooling is handled automatically
\* Don’t manually manage database connections
\* Use Laravel’s normal database usage patterns

**Caching:**
\* Cache is shared across all workers
\* Use Laravel’s cache normally
\* Be aware of cache invalidation across workers

## 4 Laravel Scout

### 4.1 Package Overview

**Package Name**: `laravel/scout`
**Version**: `^10.20`
**Purpose**: Full-text search for Eloquent models
**Architectural Role**: Provides search functionality across models

### 4.2 Installation Verification

``` bash
composer show laravel/scout
```

## 5 Environment Variables

| Variable         | Description                        | Default    |
|------------------|------------------------------------|------------|
| QUEUE_CONNECTION | Queue driver (redis/database/sync) | sync       |
| REDIS_HOST       | Redis server host                  | 127.0.0.1  |
| REDIS_PORT       | Redis server port                  | 6379       |
| OCTANE_SERVER    | Octane server (roadrunner/swoole)  | roadrunner |

## 6 Next Steps

[Observability →](090-observability.md)

## 7 Navigation

[← Fortify and WorkOS Authentication Setup](070-auth-security.md) | [↑ Top](#horizon-and-octane-queue-management) | [Telescope, Activity Log, and Health Checks →](090-observability.md)
