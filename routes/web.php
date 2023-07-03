<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/user', fn () => User::find(1));

Route::get('/user/reset', fn () => User::query()->update(['money' => 0]) ? User::find(1) : 'No User');

Route::get('/user/{user}/add-money/{way}', function (User $user, int $way) {
    // 1. add and save
    $first = function (User $user) {
        $user->update(['money' => $user->money + 1]);
    };

    // 2. update for lock
    $second = function (User $user) {
        $user = $user->lockForUpdate()->find(1);
        $user->update(['money' => $user->money + 1]);
    };

    // 3. increment
    $third = function (User $user) {
        $user->update(['money' => DB::raw('money + 1')]);
    };

    DB::transaction(
        fn () => (match ($way) {
            1 => $first,
            2 => $second,
            3 => $third
        })($user)
    );


    return User::find(1);
});
