<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Printer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private string $names = 'HP LaserJet Pro M404
HP LaserJet Pro MFP 3101
HP ENVY Inspire 7955e
HP Color LaserJet CP3525
HP Photosmart C4272
HP DeskJet 2755e
Epson EcoTank ET-2800
Brother MFC-J1010DW
Canon PIXMA G6020
Fargo DTC 1250E Printer
Lexmark CX725de
Kyocera EcoSys
Samsung ProXpress C2620 DW
Xerox C315
OKI C330dn';
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        foreach (explode("\n", $this->names) as $name) {
            if (trim($name)) {
                Printer::factory()->create(['name' => $name, 'updated_at' => now()->subDays(7)]);
            }
        }
    }
}
