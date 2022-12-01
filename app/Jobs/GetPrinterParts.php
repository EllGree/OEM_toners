<?php

namespace App\Jobs;

use App\Models\Part;
use App\Models\Printer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;

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
        foreach ($this->parsePrinter() as $parts) {
            if(!$parts) continue;
            $parts['printer_id'] = $printer->getKey();
            Part::factory($parts)->create();
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
    private function fetchPrinter() {
        $term = urlencode($this->printerName);
        $client = new Client();
        $response = $client->get('https://www.staples.com/searchux/common/api/v1/searchProxy?categoryId=12328&term='.$term);
        return $response->getBody()->getContents();
    }

    private function parsePrinter($json = false) {
        $parts = [];
        if($json === false) {
            $json = $this->fetchPrinter();
        }
        try {
            $obj = json_decode($json);
            if(empty($obj->originalQuery)) { // No printer name found in reply
                return $parts;
            }
            foreach($obj->products as $product) {
                $part = $this->parsePart($product);
                if($part) $parts[] = $part;
            }
        } catch (\Exception $e) {
            return $parts;
        }
        return $parts;
    }

    private function parsePart($p) {
        $price = $yield = intval($name = $type = $color = 'unknown');
        if (!isset($p->title)) { // No product name found in parts
            return false;
        }
        $name = $p->title;
        $price = $p->priceValue ?? preg_replace('/[^0-9.-]+/', '', $p->price);
        if (isset($p->inkAndTonerDetails) && count($p->inkAndTonerDetails)>0) {
            $prevColor = '';
            foreach($p->inkAndTonerDetails as $d) {
                if (isset($d->yieldPerPage)) {
                    $yield = intval(preg_replace('/[^0-9]+/', '', $d->yieldPerPage));
                }
                if (isset($d->yieldColor)) {
                    $color = strtolower($d->yieldColor);
                    if(str_contains($color, 'cyan/magenta/yellow')) {
                        $color = 'tri-color';
                    }
                    if($prevColor && $prevColor !== $color) {
                        $color = 'tri-color';
                    }
                    $prevColor = $color;
                }
                if (isset($d->yieldType)) {
                    if (str_contains(strtolower($d->yieldType), 'standard')) {
                        $type = 'standard';
                    } else if (str_contains(strtolower($d->yieldType), 'high yield')) {
                        $type = 'high yield';
                    } else if (str_contains(strtolower($d->yieldType), 'economy')) {
                        $type = 'economy';
                    } else if (str_contains(strtolower($d->yieldType), 'ribbon')) {
                        $type = 'ribbon';
                    } else {
                        $type = strtolower($d->yieldType);
                    }
                }
            }
        } else { // No inkAndTonerDetails section?
            if (isset($p->description->specification)) foreach ($p->description->specification as $s) {
                if (str_contains(strtolower($s->name), 'type')) $type = strtolower($s->value);
                else if (str_contains(strtolower($s->name), 'color')) $color = strtolower($s->value);
                else if (str_contains(strtolower($s->name), 'yield')) $yield = intval($s->value);
                if($type === 'printer ribbon') $type = 'ribbon';
            }
            if ($yield === 0 && isset($p->description->bullets)) foreach($p->description->bullets as $b) {
                if (str_contains(strtolower($b), 'yield')) {
                    $yield = filter_var($b, FILTER_SANITIZE_NUMBER_INT);
                }
            }
        }
        // Post-process color
        if (str_contains(strtolower($name), 'tri-color') || (str_contains(strtolower($name), 'cyan') &&
            str_contains(strtolower($name), 'magenta') &&
            str_contains(strtolower($name), 'yellow'))) {
            $color = 'tri-color';
        }
        // Post-process type
        if ($type === 'unknown' || empty($type)) {
            if (str_contains(strtolower($name), 'high yield')) $type = 'high yield';
            else if (str_contains(strtolower($name), 'drum unit')) $type = 'drum unit';
            else if (str_contains(strtolower($name), 'unit')) $type = 'unit';
            else if (str_contains(strtolower($name), 'ribbon')) $type = 'ribbon';
        }
        if (str_contains(strtolower($name), 'waste toner')) {
            $type = 'waste toner';
            $color = 'not applicable';
        }
        return compact(['name', 'type', 'color', 'price', 'yield']);
    }

}
