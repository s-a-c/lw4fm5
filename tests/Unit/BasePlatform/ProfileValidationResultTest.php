<?php

declare(strict_types=1);

use App\Data\ProfileValidationResultData;
use App\Enums\ValidationStatus;

it('exposes helpers for pass, warning and fail statuses', function (): void {
    $pass = new ProfileValidationResultData('native', ValidationStatus::Pass);
    $warn = new ProfileValidationResultData('native', ValidationStatus::Warning);
    $fail = new ProfileValidationResultData('native', ValidationStatus::Fail);

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
