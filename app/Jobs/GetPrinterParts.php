<?php

namespace App\Jobs;

use App\Models\Part;
use App\Models\Printer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
     * @return void
     */
    public function handle()
    {
        \DB::beginTransaction();

        $printer = Printer::firstOrCreate(['name' => $this->printerName]);
        // Delete all existing parts before retrieval
        $printer->parts()->delete();

        // TODO this code should get printer Parts for the given printer name
        $part = Part::factory(['name'=> 'some name', 'printer_id' => $printer->getKey()])->create();

        // Throw exception if something wrong, so transaction will be rolled back.

        // If no parts are found -- delete this Printer item.
        if (!count($printer->parts)) {
            $printer->delete();
        }

        // Update Printer updated_at attribute.
        $printer->touch();
        \DB::commit();
    }
}
