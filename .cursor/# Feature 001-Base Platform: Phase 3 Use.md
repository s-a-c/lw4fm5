# Feature 001-Base Platform: Phase 3 User Story 1: Test Output

```log
󰀵 s-a-c …/lw4fm5   001-base-platform-phase-3-user-story-1 ✘!?   v8.5.0  
❯ php artisan test --filter=BasePlatform

Deprecated: The __sleep() serialization magic method has been deprecated. Implement __serialize() instead (or in addition, if support for old PHP versions is necessary) in /Users/s-a-c/Herd/lw4fm5/vendor/nesbot/carbon/src/Carbon/CarbonInterface.php on line 854

Deprecated: Constant PDO::MYSQL_ATTR_SSL_CA is deprecated since 8.5, use Pdo\Mysql::ATTR_SSL_CA instead in /Users/s-a-c/Herd/lw4fm5/vendor/laravel/framework/config/database.php on line 64

Deprecated: Constant PDO::MYSQL_ATTR_SSL_CA is deprecated since 8.5, use Pdo\Mysql::ATTR_SSL_CA instead in /Users/s-a-c/Herd/lw4fm5/vendor/laravel/framework/config/database.php on line 84

   INFO  Deprecated: Method ReflectionMethod::setAccessible() is deprecated since 8.5, as it has no effect in /Users/s-a-c/Herd/lw4fm5/vendor/nunomaduro/collision/src/Adapters/Phpunit/ConfigureIO.php on line 37.


   DEPR  Tests\Unit\BasePlatform\BasePlatformMetricsTest
  ! it normalizes metric names and forwards payload to configured channel → Cons… 0.24s  
  ! it records bootstrap duration helper metric → Constant PDO::MYSQL_ATTR_SSL_C… 0.03s  

   DEPR  Tests\Unit\BasePlatform\BootstrapRecoveryTest
  ! it provides credential onboarding guidance when a secret is missing → Consta… 0.03s  
  ! it surfaces offline bootstrap recovery steps → Constant PDO::MYSQL_ATTR_SSL_… 0.03s  

   DEPR  Tests\Unit\BasePlatform\CredentialPolicyTest
  ! it casts rotation interval to an integer → Constant PDO::MYSQL_ATTR_SSL_CA i… 0.04s  
  ! it filters policies by context using the query scope → Constant PDO::MYSQL_A… 0.04s  

   DEPR  Tests\Feature\BasePlatform\BootstrapContainerProfileTest
  ! it validates the container profile exclusively when requested → Constant PDO… 0.04s  

   DEPR  Tests\Feature\BasePlatform\BootstrapNativeProfileTest
  ! it validates only the native profile when requested → Constant PDO::MYSQL_AT… 0.04s  

   DEPR  Tests\Feature\BasePlatform\BootstrapWorkflowTest
  ! it delegates bootstrap execution to the runner for a supported profile → Con… 0.04s  
  ! it fails fast when requesting an unsupported profile → Constant PDO::MYSQL_A… 0.04s  

   DEPR  Tests\Feature\BasePlatform\ParityCheckTest
  ! it records a passing parity report for a specific profile → Constant PDO::MY… 0.04s  

  Tests:    11 deprecated (36 assertions)
  Duration: 1.14s


󰀵 s-a-c …/lw4fm5   001-base-platform-phase-3-user-story-1 ✘!?   v8.5.0  
❯

```

