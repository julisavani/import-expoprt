<?php

namespace Database\Seeders;

use App\Models\Clarity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClaritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Clarity = [
            ['name'=>'FL'],
            ['name'=>'IF'],
            ['name'=>'VVS1'],
            ['name'=>'VVS2'],
            ['name'=>'VS1'],
            ['name'=>'VS2'],
            ['name'=>'SI1'],
            ['name'=>'SI2'],
            ['name'=>'I1'],
            ['name'=>'I2'],
            ['name'=>'I3'],
        ];

        foreach ($Clarity as $key => $value) {
            Clarity::create($value);
        }
    }
}
