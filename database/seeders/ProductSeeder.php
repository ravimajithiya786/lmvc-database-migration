<?php

use Regur\LMVC\Framework\Database\Core\Seeder;

return new class extends Seeder
{
    public function run(): void
    {
        Seeder::truncate('products');
        Seeder::insert('products',[
            [
                'name' => 'Book',
                'is_active' => 0,
                'price' => 50
            ],
            [
                'name' => 'Pen',
                'is_active' => 0,
                'price' => 10
            ],
            [
                'name' => 'Pencil',
                'is_active' => 1,
                'price' => 5
            ],
        ]);
    }
};
