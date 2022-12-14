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


        // Min yield,
        $cartridges = $printer->parts()
            // Min yield -- normal cartridges
            ->selectRaw('*,min(yield) as yield')
            ->where('name', 'like', '%Cartridge%')
//            ->orderBy('yield')
//            ->orderBy('price', 'asc')
            ->groupBy('color')->get();

        $perCopy = 0;
        /** @var Part $cartridge */
        foreach ($cartridges as $cartridge) {
            echo "$cartridge->name\t$cartridge->color\t$cartridge->yield\t$cartridge->price\t";
            echo "Per copy: $" . ($cartridge->price / $cartridge->yield) . PHP_EOL;
            $perCopy += $cartridge->price / $cartridge->yield;
        }
        $cartridgePrice = round($perCopy, 4);
        echo "Normal cartridges per copy: $" . $cartridgePrice . PHP_EOL;

        // Max yield,
        $cartridges = $printer->parts()
            ->selectRaw('*,max(yield) as yield')
            ->where('name', 'like', '%Cartridge%')
//            ->orderBy('yield', 'desc')
//            ->orderBy('price', 'asc')
            ->groupBy('color')->get();

        $perCopy = 0;
        $cartridgeYields = [];
        /** @var Part $cartridge */
        foreach ($cartridges as $cartridge) {
            echo "$cartridge->name\t$cartridge->color\t$cartridge->yield\t$cartridge->price\t";
            $cartridgeYields[] = $cartridge->yield;
            echo "Per copy: $" . ($cartridge->price / $cartridge->yield) . PHP_EOL;
            $perCopy += $cartridge->price / $cartridge->yield;
        }
        $cartridgePriceHY = round($perCopy, 4);
        echo "High Yield Cartridges per copy: $" .$cartridgePriceHY . PHP_EOL;

        // Other equipment.
        $others = $printer->parts()
                         ->selectRaw('*,max(yield) as yield')
                         ->where('name', 'not like', '%Cartridge%')
                         ->orderBy('price', 'asc')
                         ->groupBy('type', 'color')->get();
        /** @var Part $part */
        foreach ($others as $part) {
            echo "$part->name\t$part->type\t$part->color\t$part->yield\t$part->price\t";
            $copyCost = ($part->yield > 0
                ? ($part->price / $part->yield)
                : ($part->price/min($cartridgeYields))
            );
            echo "Per copy: \${$copyCost}" . PHP_EOL;
            $perCopy += $copyCost;
        }
        $equipmentPrice = round($perCopy, 4);
        echo "Total Equipment per copy: $" . $equipmentPrice . PHP_EOL;

        echo "Total Cartridge + Equipment per copy: $". ($cartridgePrice + $equipmentPrice) . PHP_EOL;
        echo "Total HY Cartridge + Equipment per copy: $". ($cartridgePriceHY + $equipmentPrice) . PHP_EOL;

        return Command::SUCCESS;
    }
}
