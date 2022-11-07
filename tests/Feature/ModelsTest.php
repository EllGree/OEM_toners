<?php

namespace Tests\Feature;

use App\Models\Printer;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testParts()
    {
        /** @var Printer $printer */
        $printer = Printer::factory()->hasParts(3)->create();
        $this->assertIsString($printer->name);
        $this->assertCount(3, $printer->parts);
        dump($printer->parts);
    }
}
