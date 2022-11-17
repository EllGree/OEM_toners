<?php

namespace App\Console\Commands;

use App\Jobs\GetPrinterParts;
use Illuminate\Console\Command;

class GetStaples extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'printer:get {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Staples Items for the Printer';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info($this->argument('model'));
        GetPrinterParts::dispatch($this->argument('model'));
        return Command::SUCCESS;
    }

}
