<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Size = [
            ['name'=>'0.18-0.22'],
            ['name'=>'0.23-0.29'],
            ['name'=>'0.30-0.39'],
            ['name'=>'0.40-0.49'],
            ['name'=>'0.50-0.59'],
            ['name'=>'0.60-0.69'],
            ['name'=>'0.70-0.79'],
            ['name'=>'0.80-0.89'],
            ['name'=>'0.90-0.99'],
            ['name'=>'1.00-1.09'],
            ['name'=>'1.10-1.19'],
            ['name'=>'1.20-1.49'],
            ['name'=>'1.50-1.69'],
            ['name'=>'1.70-1.99'],
            ['name'=>'2.00-2.19'],
            ['name'=>'2.20-2.49'],
            ['name'=>'2.50-2.69'],
            ['name'=>'2.70-2.99'],
            ['name'=>'3.00-3.99'],
            ['name'=>'4.00-4.99'],
            ['name'=>'5.00-5.99'],
            ['name'=>'6.00-9.99'],
            ['name'=>'10.00-99.99'],
        ];

        foreach ($Size as $key => $value) {
            Size::create($value);
        }
    }
}
