<?php

use Framework\db\Connection\Connection;

class SeedProducts
{

    public function up(Connection $connection)
    {
        $products = [
            [
                'name' => 'Space Tour',
                'description' => 'Take a trip with our New Test',
            ],
            [
                'name' => 'Space Tour 2',
                'description' => 'Take a trip with our New Test',
            ],
            [
                'name' => 'Space Tour 3',
                'description' => 'Take a trip with our New Test',
            ],
            [
                'name' => 'Space Tour 4',
                'description' => 'Take a trip with our New Test',
            ],
            [
                'name' => 'Space Tour 5',
                'description' => 'Take a trip with our New Test',
            ],
        ];

        foreach ($products as $product) {
            $connection
                ->query()
                ->from(CreateOrdersTable::$tableName)
                ->insert(['name', 'description',], $product);
        }
    }

}
