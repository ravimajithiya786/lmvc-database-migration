<?php

use Regur\LMVC\Framework\Database\Core\Seeder;

return new class extends Seeder
{
    public function run(): void
    {
        Seeder::truncate('vendors');
        Seeder::insert('vendors',[
            [
                'full_name' => 'Tim Wicks',
                'email' => 'tim@gmail.com',
                'mobile_no' => '7874145451',
                'is_certified' => 1
            ],
            [
                'full_name' => 'John pattrik',
                'email' => 'john@gmail.com',
                'mobile_no' => '9874515454',
                'is_certified' => 1
            ],
            [
                'full_name' => 'Michel bells',
                'email' => 'michel@gmail.com',
                'mobile_no' => '12451323212',
                'is_certified' => 0
            ]
        ]); 
    }
};
