<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function (mixed $user, mixed $id): bool {
    if (! is_object($user) || ! property_exists($user, 'id')) {
        return false;
    }
    $userIdRaw = $user->id;
    $userId = is_int($userIdRaw) ? $userIdRaw : (is_numeric($userIdRaw) ? (int) $userIdRaw : 0);
    $channelIdRaw = $id;
    $channelId = is_int($channelIdRaw) ? $channelIdRaw : (is_numeric($channelIdRaw) ? (int) $channelIdRaw : 0);

    return $userId === $channelId;
});
