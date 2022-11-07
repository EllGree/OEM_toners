<?php

namespace App\Http\Controllers;

use App\Models\Printer;

class HomeController extends Controller
{
    public function home()
    {
        $printers = Printer::all();

        return view('welcome', ['printers' => $printers]);
    }
}
