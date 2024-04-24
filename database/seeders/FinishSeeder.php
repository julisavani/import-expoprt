<?php

namespace Database\Seeders;

use App\Models\Finish;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FinishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Finish = [
            ['name'=>'8X', 'specific_type'=>'0', 'type'=>'0'],
            ['name'=>'ID', 'specific_type'=>'0', 'type'=>'0'],
            ['name'=>'EX', 'specific_type'=>'0', 'type'=>'0'],
            ['name'=>'VG', 'specific_type'=>'0', 'type'=>'0'],
            ['name'=>'G', 'specific_type'=>'0', 'type'=>'0'],
            ['name'=>'F', 'specific_type'=>'0', 'type'=>'0'],
            ['name'=>'P', 'specific_type'=>'0', 'type'=>'0'],
            ['name'=>'8X', 'specific_type'=>'0', 'type'=>'1'],
            ['name'=>'EX', 'specific_type'=>'0', 'type'=>'1'],
            ['name'=>'VG', 'specific_type'=>'0', 'type'=>'1'],
            ['name'=>'G', 'specific_type'=>'0', 'type'=>'1'],
            ['name'=>'F', 'specific_type'=>'0', 'type'=>'1'],
            ['name'=>'P', 'specific_type'=>'0', 'type'=>'1'],
            ['name'=>'8X', 'specific_type'=>'0', 'type'=>'2'],
            ['name'=>'EX', 'specific_type'=>'0', 'type'=>'2'],
            ['name'=>'VG', 'specific_type'=>'0', 'type'=>'2'],
            ['name'=>'G', 'specific_type'=>'0', 'type'=>'2'],
            ['name'=>'F', 'specific_type'=>'0', 'type'=>'2'],
            ['name'=>'P', 'specific_type'=>'0', 'type'=>'2'],
            ['name'=>'8X', 'specific_type'=>'1', 'type'=>'3'],
            ['name'=>'3X+', 'specific_type'=>'1', 'type'=>'3'],
            ['name'=>'3VG+', 'specific_type'=>'1', 'type'=>'3'],
        ];

        foreach ($Finish as $key => $value) {
            Finish::create($value);
        }
    }
}
