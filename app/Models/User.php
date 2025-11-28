<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Data\UserSettingsData;
use App\Services\Theme\ThemeService;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property UserSettingsData|null $settings
 */
final class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'settings',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn (string $word): string => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
        return true;
    }

    // Safety check: Ensure we always have a data object, even if DB column is null
    // Also validate and correct settings on every access (T017, FR-009, FR-027)
    // Silently persist corrected settings (T018, FR-028)
    protected static function booted(): void
    {
        /** @param User $user */
        self::retrieved(function (self $user): void {
            if ($user->settings === null) {
                $user->settings = new UserSettingsData();
            } else {
                // Validate and correct settings on every access using ThemeService
                // This ensures invalid theme/flavor/accent combinations are auto-corrected
                $themeService = app(ThemeService::class);
                $validated = $themeService->resolveThemeData($user->settings);

                // Update settings if validation corrected any values
                if ($user->settings->theme !== $validated->theme ||
                    $user->settings->flavor !== $validated->flavor ||
                    $user->settings->accent !== $validated->accent) {
                    $user->settings = new UserSettingsData(
                        theme: $validated->theme,
                        flavor: $validated->flavor,
                        accent: $validated->accent,
                    );

                    // Silently persist corrected settings (T018, FR-028)
                    // Use updateQuietly to avoid triggering events
                    $user->updateQuietly(['settings' => $user->settings]);
                }
            }
        });

        // Clean up theme preferences when user account is deleted (T018a, FR-030)
        /** @param User $user */
        self::deleting(function (self $user): void {
            // Theme preferences are stored in the settings column on the users table
            // When the user is deleted, the settings column is automatically deleted
            // This handler ensures any additional theme-related cleanup can be performed here
            // For now, the settings column deletion is handled by the database cascade
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // This automatically converts the JSON DB column to/from the PHP object
            'settings' => UserSettingsData::class,
        ];
    }
}
