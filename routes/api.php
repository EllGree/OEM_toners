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

Route::get('/printer/{printer}', function(Printer $printer) {
    $reply = (object) $printer->getAttributes();
    $reply->parts = $printer->parts;
    $reply->groups = $printer->groupsDebug();
    return response()->json($reply, 200, [], JSON_PRETTY_PRINT);
});

Route::post('/printer/{id}', function($id) {
    $printer = Printer::whereId($id)->first();
    $data = [];
    if(!empty($_POST['manufacturer']) && !empty($_POST['model'])) $_POST['name'] = $_POST['manufacturer'] . ' ' . $_POST['model'];
    if(!empty($_POST['name']))  $data['name'] = $_POST['name'];
    if(!empty($_POST['coverage']))  $data['coverage'] = $_POST['coverage'];
    if(count($data)) $printer->update($data);
    $reply = json_decode(json_encode($printer->getAttributes()));
    return response(json_encode($reply))->header('Content-type', 'application/json');
});

Route::delete('/printer/{id}', function($id) {
    $ids = strstr($id, ',') ? explode(',', $id) : [$id];
    foreach ($ids as $i) {
        $printer = Printer::whereId($i)->first();
        if (!$printer) continue;
        foreach ($printer->parts()->get() as $part) {
            $part->delete();
        }
        $printer->delete();
    }
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

Route::get('/export/{ids}', function($ids) {
    $txt = "Name,Coverage (%),Standard ($),High Yield ($)\n";
    $printers = Printer::all();
    $ids = $ids == 'all'? false : explode(',', $ids);
    foreach ($printers as $printer) {
        if(!$ids || in_array($printer->getKey(), $ids)) {
            $price = $hyprice = 0;
            $groups = $printer->getGroups();
            foreach ($groups->normal as $p) $price += $p->perCopy;
            foreach ($groups->high as $p) $hyprice += $p->perCopy;
            foreach ($groups->other as $p) {
                $price += $p->perCopy;
                $hyprice += $p->perCopy;
            }
            $txt .= "\"{$printer->getAttribute('name')}\",{$printer->getAttribute('coverage')},{$price},{$hyprice}\n";
        }
    }
    return response($txt)->header('Content-type', 'text/plain');
});
