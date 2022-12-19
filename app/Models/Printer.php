<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Printer
 * @property int $id
 * @property string $name
 * @property int $coverage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Part[] $parts
 * @property-read int|null $parts_count
 * @method static \Database\Factories\PrinterFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Printer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Printer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Printer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Printer whereCoverage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Printer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Printer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Printer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Printer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Printer extends Model {

    use HasFactory;

    public $fillable = ['name', 'coverage'];

    public function parts() {

        return $this->hasMany(Part::class);
    }

    public function groupsDebug() {

        $response = '<pre>';
        // Min yield,
        $cartridges = $this->parts()
            // Min yield -- normal cartridges
                           ->selectRaw('*,min(yield) as yield')
                           ->where('name', 'like', '%Cartridge%')
                           ->groupBy('color')->get();

        $perCopy = 0;
        /** @var Part $cartridge */
        foreach ($cartridges as $cartridge) {
            $response .= "$cartridge->name\t$cartridge->color\t$cartridge->yield\t$cartridge->price\t";
            if($cartridge->yield>0) {
                $response .= "Per copy: $" . ($cartridge->price / $cartridge->yield) . PHP_EOL;
                $perCopy += $cartridge->price / $cartridge->yield;
            }
        }
        $cartridgePriceRaw = round($perCopy, 4);
        $cartridgePrice = round($cartridgePriceRaw * $this->coverage / 5, 4);

        $response .= "<b>Normal Cartridges per copy: \${$cartridgePriceRaw} (5%), \${$cartridgePrice} ({$this->coverage}%)</b>" .
                     PHP_EOL;

        // Max yield,
        $cartridges = $this->parts()
                           ->selectRaw('*,max(yield) as yield')
                           ->where('name', 'like', '%Cartridge%')
                           ->groupBy('color')->get();

        $perCopy = 0;
        $cartridgeYields = [];
        /** @var Part $cartridge */
        foreach ($cartridges as $cartridge) {
            $response .= "$cartridge->name\t$cartridge->color\t$cartridge->yield\t$cartridge->price\t";
            $cartridgeYields[] = $cartridge->yield;
            if($cartridge->yield>0) {
                $response .= "Per copy: $" . ($cartridge->price / $cartridge->yield) . PHP_EOL;
                $perCopy += $cartridge->price / $cartridge->yield;
            }
        }
        $cartridgePriceRaw = round($perCopy, 4);
        $cartridgePriceHY = round($cartridgePriceRaw * $this->coverage / 5, 4);
        $response .= "<b>High Yield Cartridges per copy: \${$cartridgePriceRaw} (5%), \${$cartridgePriceHY} ({$this->coverage}%)</b>" .
                     PHP_EOL . PHP_EOL;

        // Other equipment.
        $others =
            $this->parts()->selectRaw('*,max(yield) as yield')->where('name', 'not like', '%Cartridge%')->orderBy('price', 'asc')
                 ->groupBy('type', 'color')->get();
        /** @var Part $part */
        $perCopy = 0;
        foreach ($others as $part) {
            $response .= "$part->name\t$part->type\t$part->color\t$part->yield\t$part->price\t";
            $copyCost = ($part->yield > 0 ? ($part->price / $part->yield) : ($part->price / min($cartridgeYields)));
            $response .= "Per copy: \${$copyCost}" . PHP_EOL;
            $perCopy += $copyCost;
        }
        $equipmentPrice = round($perCopy, 4);
        $response .= "<b>Total Equipment per copy: $" . $equipmentPrice . "</b>" . PHP_EOL;

        $response .= "<br><b>Total Cartridge + Equipment per copy: $" . ($cartridgePrice + $equipmentPrice) . PHP_EOL;
        $response .= "Total HY Cartridge + Equipment per copy: $" . ($cartridgePriceHY + $equipmentPrice) . PHP_EOL;
        $response .= "<pre>";

        return $response;
    }
}
