<?php

use Regur\LMVC\Framework\Database\Core\Seeder;

return new class extends Seeder
{
    public function run(): void
    {
        Seeder::truncate('products');
        Seeder::insert('products',[
            [
                'title' => 'Book',
                'category_id' => 1,
                'is_active' => 0,
                'price' => 50,
                'stock' => 400
            ],
            [
                'title' => 'Pen',
                'category_id' => 3,
                'is_active' => 0,
                'price' => 10,
                'stock' => 500
            ],
            [
                'title' => 'Pencil',
                'category_id' => 5,
                'is_active' => 1,
                'price' => 5,
                'stock' => 600
            ],
        ]);
    }
};
