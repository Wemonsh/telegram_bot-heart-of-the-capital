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

Route::get('redirect', function (\Illuminate\Http\Request $request) {
    if ($request->type = 1) {
        $q = \App\Models\Questionnaire::where('id', $request->id)->first();

        if ($q !== null && $q->status === 1) {
            $q->status = 2;
            $q->save();
            return redirect('https://t.me/+wu2etczoVqo1MDNi');

        }
    } elseif($request->type = 2) {
        $q = \App\Models\Questionnaire::where('id', $request->id)->first();

        if ($q !== null && $q->status === 1) {
            $q->status = 2;
            $q->save();
            return redirect('https://t.me/+4rRbTAIvQzZjZGUy');

        }
    }
    abort(403, 'Вы уже ранее совершали переход по ссылке');
});

Route::match(['get', 'post'],'/telegram', [App\Http\Controllers\BotManController::class, '__invoke']);
