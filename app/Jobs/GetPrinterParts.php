<?php

namespace App\Jobs;

use App\Models\Part;
use App\Models\Printer;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;
// use GuzzleHttp\Psr7\Request;


class GetPrinterParts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public string $printerName)
    {
        //
    }

    /**
     * Execute the job.
     *
     */
    public function handle()
    {
        \DB::beginTransaction();

        $printer = Printer::firstOrCreate(['name' => $this->printerName]);
        // Delete all existing parts before retrieval
        $printer->parts()->delete();

        // Get printer Parts for the given printer name
        $json = $this->fetchPrinter($printer->name);
        foreach ($this->parsePrinter($json) as $part) {
            $p = Part::factory(['name' => 'some name', 'printer_id' => $printer->getKey()])->create();
        }

        // Throw exception if something wrong, so transaction will be rolled back.

        // If no parts are found -- delete this Printer item.
        if (!count($printer->parts)) {
            $printer->delete();
        }

        // Update Printer updated_at attribute.
        $printer->touch();
        \DB::commit();
        return $printer;
    }

    // Helpers:
    private function fetchPrinter($term) {
        $client = new Client();
        $response = $client->get('https://www.staples.com/searchux/common/api/v1/searchProxy?categoryId=12328&term='.urlencode($term));
        return $response->getBody()->getContents();
    }

    private function parsePrinter($json) {
        $parts = [];
        $arr = json_decode($json);
        dd($arr);
        foreach($arr as $part) {
            $parts[] = $this->parsePart($part);
        }
        /* JS code to porting:
        var ret = {name:'',type:'monochrome',cost:0,yeld:0,ccost:0,cyeld:0,details:[]};
        if (obj.originalQuery) ret.name = obj.originalQuery;
        obj.products.forEach(function(p) {
            var p = app.parseProduct(p);
            if (p && ret.details.filter(function(d) { return d.color === p.color;}).length < 1) {
                ret.details.push(p);
            }
        });
        ret.details.forEach(function(p) {
            if (p.color === 'black') {
                if (ret.cost === 0) ret.cost = p.cost;
                if (ret.yeld === 0) ret.yeld = p.yeld;
            } else {
                ret.type = 'color';
                if (ret.ccost === 0) ret.ccost = p.cost;
                if (ret.cyeld === 0) ret.cyeld = p.yeld;
            }
        });
         */
        return $parts;
    }

    private function parsePart($obj) {
        $part = ['name'=>'', 'type'=>'', 'price'=>0, 'yield'=>0];
        /* JS code to porting:
        var standard = false, ret = {name:p.title,color:'black',yeld:0,cost:0};
        if (p.title.match(/Tri-Color/)) ret.color = 'tri-color';
        if (p.priceValue) ret.cost = p.priceValue;
        else if (p.price) ret.cost = parseFloat(p.price.replace(/[^0-9.-]+/g, ''));
        if (p.inkAndTonerDetails) p.inkAndTonerDetails.forEach(function(d) {
            if (d.yieldColor && !ret.color) ret.color = d.yieldColor.toLowerCase();
            if (d.yieldType && d.yieldType.match(/Standard/)) standard = true;
            if (d.yieldPerPage) ret.yeld = parseInt(d.yieldPerPage.replace(/[^0-9]+/g, ''));
        });
        if (p.description.specification) p.description.specification.forEach(function(s){
            if (s.name.match(/Cartridge Yield Type/) && s.value.match(/^Standard/)) standard = true;
            else if (s.name === 'Ink or Toner Color' || s.name === 'True Color') ret.color = ret.color === 'black' ? s.value.toLowerCase() : ret.color;
            else if (s.name.match(/^(Page Yield|Yield per Cartridge)/) && ret.yeld === 0) ret.yeld = parseInt(s.value.replace(/[^0-9]+/g, ''));
        });
        // Post-processing:
        if (p.title.match(/High Yield/)) standard = false;
        console.log(standard ? 'standard' : 'non-standard', {p}, {ret});
        if (!standard) return null;
        if (ret.color.match('cyan/magenta/yellow')) ret.color = 'tri-color';
         */
        return $part;
    }

}
