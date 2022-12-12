<?php

namespace App\Console\Commands;

use App\Jobs\GetPrinterParts;
use App\Models\Printer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdatePrinter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'printer:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Next Printer for the Update (and Update parts)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($printer = Printer::orderBy('updated_at')
            ->where('updated_at', '<', Carbon::now()->subDays(3))
            ->first()) {
            $this->info($printer->name);
            GetPrinterParts::dispatch($printer->name);
        }
        return Command::SUCCESS;
    }
}
