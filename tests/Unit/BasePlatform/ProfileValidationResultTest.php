<?php

declare(strict_types=1);

use App\Services\BasePlatform\ProfileValidationResult;

it('exposes helpers for pass, warning and fail statuses', function (): void {
    $pass = new ProfileValidationResult('native', ProfileValidationResult::STATUS_PASS);
    $warn = new ProfileValidationResult('native', ProfileValidationResult::STATUS_WARNING);
    $fail = new ProfileValidationResult('native', ProfileValidationResult::STATUS_FAIL);

    expect($pass->isPass())->toBeTrue()
        ->and($pass->isWarning())->toBeFalse()
        ->and($pass->isFail())->toBeFalse();

    expect($warn->isPass())->toBeFalse()
        ->and($warn->isWarning())->toBeTrue()
        ->and($warn->isFail())->toBeFalse();

    expect($fail->isPass())->toBeFalse()
        ->and($fail->isWarning())->toBeFalse()
        ->and($fail->isFail())->toBeTrue();
});
