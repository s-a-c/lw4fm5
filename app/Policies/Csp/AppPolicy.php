<?php

declare(strict_types=1);

namespace App\Policies\Csp;

final class AppPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @phpstan-return false
     */
    public function viewAny(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @phpstan-return false
     */
    public function view(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @phpstan-return false
     */
    public function create(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @phpstan-return false
     */
    public function update(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @phpstan-return false
     */
    public function delete(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @phpstan-return false
     */
    public function restore(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @phpstan-return false
     */
    public function forceDelete(): bool
    {
        return false;
    }
}
