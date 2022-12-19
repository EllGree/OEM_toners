<?php

namespace App\Console\Commands;

use App\Jobs\GetPrinterParts;
use App\Models\Part;
use App\Models\Printer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Events\QueryExecuted;

class GetPrinter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'printer:group {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the Printer and display its groups)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$printer = Printer::whereName($this->argument('name'))->first()) {
            $this->error($this->argument('name') . " printer not found!");
        }
//        \DB::listen(function (QueryExecuted $sql) {
//            var_dump($sql->sql, $sql->bindings, $sql->time);
//        });

        echo $printer->groupsDebug();

        return Command::SUCCESS;
    }
}
