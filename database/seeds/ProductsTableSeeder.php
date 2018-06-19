<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Product;
class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->truncate();
        DB::table('products')->insert(array(
            array('product_name'=>'Apparel'),
            array('product_name'=>'Automotive Parts'),
            array('product_name'=>'Accessories'),
            array('product_name'=>'Personal Electronics(Mobile Phones,Laptops etc)'),
            array('product_name'=>'Electronics Accessories (Cases, Chargers etc)'),
            array('product_name'=>'Gadgets'),
            array('product_name'=>'Jewellery'),
            array('product_name'=>'Cosmetics'),
            array('product_name'=>'Stationery'),
            array('product_name'=>'Handicrafts'),
            array('product_name'=>'Home-made Items'),
            array('product_name'=>'Footwear'),
            array('product_name'=>'Watches'),
            array('product_name'=>'Leather Items'),
            array('product_name'=>'Organic and Health Products'),
            array('product_name'=>'Appliances, Consumer Electronics'),
            array('product_name'=>'Home Decor, Interior Items'),
            array('product_name'=>'Toys'),
            array('product_name'=>'Pet Supplies'),
            array('product_name'=>'Athletics and Fitness Items'),
            array('product_name'=>'Vouchers, Coupons'),
            array('product_name'=>'Marketplace'),
            array('product_name'=>'Documents, Letters'),
            array('product_name'=>'Other'),

        ));
    }
}
