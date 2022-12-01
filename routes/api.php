<?php

use App\Jobs\GetPrinterParts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Printer;
use App\Models\Part;

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
    if (!$printer = Printer::whereName($_POST['term'])->first()) {
        $printer = (new GetPrinterParts($_POST['term']))->handle();
    }
    $reply = json_decode(json_encode($printer->getAttributes()));
    $reply->parts = $printer->parts()->get();
    return response(json_encode($reply))
        ->header('Content-type', 'application/json');
    }
);
