<?php

declare(strict_types=1);

test('example', function () {
    $response = $this->get('/');

    expect($response)->assertOk();
});
