<?php

namespace Database\Seeders;

use App\Models\Fluorescence;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FluorescenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Fluorescence = [
            ['name'=>'NONE', 'type'=>'0'],
            ['name'=>'VST', 'type'=>'0'],
            ['name'=>'STG', 'type'=>'0'],
            ['name'=>'MED', 'type'=>'0'],
            ['name'=>'FNT', 'type'=>'0'],
            ['name'=>'SLT', 'type'=>'0'],
            ['name'=>'VSLT', 'type'=>'0'],
            ['name'=>'B', 'type'=>'1'],
            ['name'=>'W', 'type'=>'1'],
            ['name'=>'Y', 'type'=>'1'],
            ['name'=>'O', 'type'=>'1'],
            ['name'=>'R', 'type'=>'1'],
            ['name'=>'G', 'type'=>'1'],
            ['name'=>'N', 'type'=>'1'],
            ['name'=>'NONE', 'type'=>'2'],
            ['name'=>'BROWN TINGE1', 'type'=>'2'],
            ['name'=>'BROWN TINGE2', 'type'=>'2'],
            ['name'=>'BROWN TINGE3', 'type'=>'2'],
            ['name'=>'MIX TINGE1', 'type'=>'2'],
            ['name'=>'MIX TINGE2', 'type'=>'2'],
            ['name'=>'MIX TINGE3', 'type'=>'2'],
            ['name'=>'BLUE1', 'type'=>'2'],
            ['name'=>'BLUE2', 'type'=>'2'],
            ['name'=>'BLUE3', 'type'=>'2'],
        ];

        foreach ($Fluorescence as $key => $value) {
            Fluorescence::create($value);
        }
    }
}
