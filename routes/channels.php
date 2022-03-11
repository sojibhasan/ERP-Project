<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('customer-limit-approval-channel.{user_id}', function ($user, $user_id) {
    return (int) $user->id === (int) $user_id;
});

Broadcast::channel('customer-limit-approved.{user_id}', function ($user, $user_id) {
    return (int) $user->id === (int) $user_id;
});
Broadcast::channel('stock-transfer-request-complete.{user_id}', function ($user, $user_id) {
    return (int) $user->id === (int) $user_id;
});
