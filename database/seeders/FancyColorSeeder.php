<?php

namespace Database\Seeders;

use App\Models\FancyColor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FancyColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $FancyColor = [
            ['name'=>'Yellow', 'image'=>'https://server.delightdiamonds.com/storage/fancycolor/Yellow-230317112439.png', 'type'=>'0'],
            ['name'=>'Orange', 'image'=>'https://server.delightdiamonds.com/storage/fancycolor/Orange-230419113632.jpg', 'type'=>'0'],
            ['name'=>'Pink', 'image'=>'https://server.delightdiamonds.com/storage/fancycolor/Pink-230317112638.png', 'type'=>'0'],
            ['name'=>'Blue', 'image'=>'https://server.delightdiamonds.com/storage/fancycolor/Blue-230317112709.png', 'type'=>'0'],
            ['name'=>'Green', 'image'=>'https://server.delightdiamonds.com/storage/fancycolor/Green-230317112720.png', 'type'=>'0'],
            ['name'=>'Brown', 'image'=>'https://server.delightdiamonds.com/storage/fancycolor/Brown-230317112737.png', 'type'=>'0'],
            ['name'=>'Red', 'image'=>'https://server.delightdiamonds.com/storage/fancycolor/Red-230317112754.png', 'type'=>'0'],
            ['name'=>'White', 'image'=>'https://server.delightdiamonds.com/storage/fancycolor/White-230317112806.png', 'type'=>'0'],
            ['name'=>'Violet', 'image'=>'https://server.delightdiamonds.com/storage/fancycolor/Violet-230317112818.png', 'type'=>'0'],
            ['name'=>'Purple', 'image'=>'https://server.delightdiamonds.com/storage/fancycolor/Purple-230317112834.png', 'type'=>'0'],
            ['name'=>'Gray', 'image'=>'https://server.delightdiamonds.com/storage/fancycolor/Gray-230317112856.png', 'type'=>'0'],
            ['name'=>'Olive', 'image'=>'https://server.delightdiamonds.com/storage/fancycolor/Olive-230317112908.png', 'type'=>'0'],
            ['name'=>'Black', 'image'=>'https://server.delightdiamonds.com/storage/fancycolor/Black-230317112919.png', 'type'=>'0'],
            ['name'=>'Other', 'image'=>'https://server.delightdiamonds.com/storage/fancycolor/Other-230317112931.png', 'type'=>'0'],
            ['name'=>'Yellow', 'type'=>'1'],
            ['name'=>'Yellowish', 'type'=>'1'],
            ['name'=>'Pink', 'type'=>'1'],
            ['name'=>'Pinkish', 'type'=>'1'],
            ['name'=>'Blue', 'type'=>'1'],
            ['name'=>'Bluish', 'type'=>'1'],
            ['name'=>'Red', 'type'=>'1'],
            ['name'=>'Reddish', 'type'=>'1'],
            ['name'=>'Green', 'type'=>'1'],
            ['name'=>'Greenish', 'type'=>'1'],
            ['name'=>'Purple', 'type'=>'1'],
            ['name'=>'Purplish', 'type'=>'1'],
            ['name'=>'Orange', 'type'=>'1'],
            ['name'=>'Orangy', 'type'=>'1'],
            ['name'=>'Violet', 'type'=>'1'],
            ['name'=>'Violetish', 'type'=>'1'],
            ['name'=>'Gray', 'type'=>'1'],
            ['name'=>'Grayish', 'type'=>'1'],
            ['name'=>'Black', 'type'=>'1'],
            ['name'=>'Brown', 'type'=>'1'],
            ['name'=>'Brownish', 'type'=>'1'],
            ['name'=>'Champagne', 'type'=>'1'],
            ['name'=>'Cognac', 'type'=>'1'],
            ['name'=>'Chameleon', 'type'=>'1'],
            ['name'=>'White', 'type'=>'1'],
            ['name'=>'Other', 'type'=>'1'],
            ['name'=>'Fancy Deep', 'type'=>'2'],
            ['name'=>'Fancy Dark', 'type'=>'2'],
            ['name'=>'Fancy Vivid', 'type'=>'2'],
            ['name'=>'Fancy Intense', 'type'=>'2'],
            ['name'=>'Fancy', 'type'=>'2'],
            ['name'=>'Fancy Light', 'type'=>'2'],
            ['name'=>'Light', 'type'=>'2'],
            ['name'=>'Very Light', 'type'=>'2'],
            ['name'=>'Faint', 'type'=>'2'],
        ];

        foreach ($FancyColor as $key => $value) {
            FancyColor::create($value);
        }
    }
}
