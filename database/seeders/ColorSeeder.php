<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Color = [
            ['name'=>'D'],
            ['name'=>'E'],
            ['name'=>'F'],
            ['name'=>'G'],
            ['name'=>'H'],
            ['name'=>'I'],
            ['name'=>'J'],
            ['name'=>'K'],
            ['name'=>'L'],
            ['name'=>'M'],
            ['name'=>'N'],
            ['name'=>'O-P'],
            ['name'=>'Q-R'],
            ['name'=>'S-T'],
            ['name'=>'U-V'],
            ['name'=>'W-X'],
            ['name'=>'Y-Z'],
        ];

        foreach ($Color as $key => $value) {
            Color::create($value);
        }
    }
}
