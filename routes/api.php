<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Printer;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/printers', function() {
    $printers = Printer::all();
    return response(json_encode($printers))
        ->header('Content-type', 'application/json');
});

Route::post('/printers', function() {
    return response(json_encode([$_POST['term']]))
        ->header('Content-type', 'application/json');
    }
);
