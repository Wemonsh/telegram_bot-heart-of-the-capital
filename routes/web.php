<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('test', function () {
    $url = "https://api.telegram.org/file/bot1209950271:AAE2Q1svLFkHmhUYFDiJh7O3DHIfNOAEC-c/photos/file_15.jpg";
    $contents = file_get_contents($url);
    $name = uniqid() . '_' . substr($url, strrpos($url, '/') + 1);
    Storage::disk('public')->put($name, $contents);
});

Route::match(['get', 'post'],'/telegram', [App\Http\Controllers\BotManController::class, '__invoke']);
