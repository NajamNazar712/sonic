<?php

use Illuminate\Database\Seeder;

class RetailProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('retail_products')->truncate();

        DB::table('retail_products')->insert(array(
            array('id' => 1, 'name' => 'Accessories'),
            array('id' => 2, 'name' => 'Apparel'),
            array('id' => 3, 'name' => 'Appliances and consumer electronics'),
            array('id' => 4, 'name' => 'Athletics and Fitness'),
            array('id' => 5, 'name' => 'Automotive parts'),
            array('id' => 6, 'name' => 'Cosmetics'),
            array('id' => 7, 'name' => 'Documents and letters'),
            array('id' => 8, 'name' => 'Electronic Accessories (Cases and Chargers etc.)'),
            array('id' => 9, 'name' => 'Footwear'),
            array('id' => 10, 'name' => 'Gadgets'),
            array('id' => 11, 'name' => 'Handicrafts'),
            array('id' => 12, 'name' => 'Home decor and Interior Items'),
            array('id' => 13, 'name' => 'Homemade Items'),
            array('id' => 14, 'name' => 'Jewelry'),
            array('id' => 15, 'name' => 'Leather Items'),
            array('id' => 16, 'name' => 'Marketplace'),
            array('id' => 17, 'name' => 'Organic and Health Products'),
            array('id' => 18, 'name' => 'Personal Electronics (Mobile phones and Laptops etc.)'),
            array('id' => 19, 'name' => 'Pet Supplies'),
            array('id' => 20, 'name' => 'Stationery'),
            array('id' => 21, 'name' => 'Toys'),
            array('id' => 22, 'name' => 'Vouchers and Coupons'),
            array('id' => 23, 'name' => 'Watch'),
            array('id' => 24, 'name' => 'Others')
        ));
    }
}
