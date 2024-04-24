<?php

namespace Database\Seeders;

use App\Models\Shape;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShapeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Shape = [
            ['name'=>'Round', 'icon' => 'icon-round'],
            ['name'=>'Oval', 'icon' => 'icon-Single_6'],
            ['name'=>'Pear', 'icon' => 'icon-Single_5'],
            ['name'=>'Cush Mod', 'icon' => ''],
            ['name'=>'Cush Brill', 'icon' => 'icon-Single_14'],
            ['name'=>'Emerald', 'icon' => 'icon-Single_9'],
            ['name'=>'Radiant', 'icon' => 'icon-Single_12'],
            ['name'=>'Princess', 'icon' => 'icon-Single_4'],
            ['name'=>'Asscher', 'icon' => ''],
            ['name'=>'Square', 'icon' => ''],
            ['name'=>'Marquise', 'icon' => 'icon-Single_7'],
            ['name'=>'Heart', 'icon' => 'icon-Single_8'],
            ['name'=>'Trilliant', 'icon' => 'icon-Single_13'],
            ['name'=>'Euro Cut', 'icon' => ''],
            ['name'=>'Old Miner', 'icon' => ''],
            ['name'=>'Briolette', 'icon' => ''],
            ['name'=>'Rose Cut', 'icon' => ''],
            ['name'=>'Lozenge', 'icon' => ''],
            ['name'=>'Baguette', 'icon' => ''],
            ['name'=>'Tap Baguette', 'icon' => ''],
            ['name'=>'Half Moon', 'icon' => ''],
            ['name'=>'Flanders', 'icon' => ''],
            ['name'=>'Trapezoid', 'icon' => ''],
            ['name'=>'Bullets', 'icon' => ''],
            ['name'=>'Kite', 'icon' => ''],
            ['name'=>'Shield', 'icon' => ''],
            ['name'=>'Star', 'icon' => ''],
            ['name'=>'Pentagonal', 'icon' => ''],
            ['name'=>'Hexagonal', 'icon' => 'icon-hexagonal'],
            ['name'=>'Octagonal', 'icon' => ''],
        ];

        foreach ($Shape as $key => $value) {
            Shape::create($value);
        }
    }
}
