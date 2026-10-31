<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('transactions', function (User $user): bool {
    return $user->isAdmin();
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
