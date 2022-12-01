<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Part
 *
 * @property int $id
 * @property string $name
 * @property int $printer_id
 * @property float $price
 * @property int $yeld
 * @property string|null $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\PartFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Part newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Part newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Part query()
 * @method static \Illuminate\Database\Eloquent\Builder|Part whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Part whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Part whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Part whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Part wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Part wherePrinterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Part whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Part whereYeld($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Printer|null $printer
 */
class Part extends Model
{
    use HasFactory;

    public $fillable = ['name', 'type', 'price', 'color', 'yield'];

    public function printer() {
        return $this->belongsTo(Printer::class);
    }
}
