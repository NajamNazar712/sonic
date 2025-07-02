<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Http\Models\Product;
use DB;

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
            array('product_name'=>'Apparel', 'parent_product_id' =>2  ),
            array('product_name'=>'Automotive Parts', 'parent_product_id' =>1),
            array('product_name'=>'Accessories', 'parent_product_id' =>1),
            array('product_name'=>'Personal Electronics (Mobile Phones, Laptops, etc)', 'parent_product_id' =>1),
            array('product_name'=>'Electronics Accessories (Cases, Chargers, etc)', 'parent_product_id' =>1),
            array('product_name'=>'Gadgets', 'parent_product_id' =>1),
            array('product_name'=>'Jewellery', 'parent_product_id' =>3),
            array('product_name'=>'Cosmetics', 'parent_product_id' =>1),
            array('product_name'=>'Stationery', 'parent_product_id' =>3),
            array('product_name'=>'Handicrafts', 'parent_product_id' =>3),
            array('product_name'=>'Home-made Items', 'parent_product_id' =>3),
            array('product_name'=>'Footwear', 'parent_product_id' =>3),
            array('product_name'=>'Watches', 'parent_product_id' =>1),
            array('product_name'=>'Leather Items', 'parent_product_id' =>3),
            array('product_name'=>'Organic and Health Products', 'parent_product_id' =>3),
            array('product_name'=>'Appliances and Consumer Electronics', 'parent_product_id' =>1),
            array('product_name'=>'Home Decor and Interior Items', 'parent_product_id' =>3),
            array('product_name'=>'Toys', 'parent_product_id' =>3),
            array('product_name'=>'Pet Supplies', 'parent_product_id' =>3),
            array('product_name'=>'Athletics and Fitness Items', 'parent_product_id' =>1),
            array('product_name'=>'Vouchers and Coupons', 'parent_product_id' =>3),
            array('product_name'=>'Marketplace', 'parent_product_id' =>3),
            array('product_name'=>'Documents and Letters', 'parent_product_id' =>3),
            array('product_name'=>'Other', 'parent_product_id' =>3),
            array('product_name'=>'Testers', 'parent_product_id' =>3),
            array('product_name'=>'Bucket Shop', 'parent_product_id' =>3),

        ));
    }
}
