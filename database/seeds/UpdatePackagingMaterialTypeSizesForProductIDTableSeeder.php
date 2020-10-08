<?php

use App\Http\Models\WMS\WmsProduct;
use App\Http\Models\WMS\WmsProductCategory;
use App\Http\Models\PackagingMaterialTypeSizes;
use App\Http\Models\Admin\GlobalSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UpdatePackagingMaterialTypeSizesForProductIDTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting = GlobalSettings::where('type', 'Packaging Material');
        if($setting->exists()){
            $setting = $setting->first();
            $sizes = PackagingMaterialTypeSizes::where('wms_product_id', 0);
            if($sizes->exists()){
                $existing_product_category = WmsProductCategory::where('name', 'Packaging Material');
                if($existing_product_category->exists()){
                    $product_type = $existing_product_category->first();
                }
                else{
                    $product_type = new WmsProductCategory();
                    $product_type->name = 'Packaging Material';
                    $product_type->user_id = $setting->setting_value;
                    $product_type->save();
                }
                $sizes= $sizes->get();
                foreach ($sizes as $size){
                    $product = new WmsProduct();
                    $product->name = $size->type->type . ' - ' . $size->size;
                    $product->sku_id = 'PM-' . $size->type->id . '-' . $size->id;
                    $product->description = 'Packaging Material Type-Size : ' . $size->type->type . '-' . $size->size;
                    $product->buffer_quantity = 1;
                    $product->status = 1;
                    $product->user_id = $setting->setting_value;
                    $product->category_id = $product_type->id;
                    $product->save();
                    $barcode = $setting->setting_value.'-'.strtoupper($product->sku_id);
                    $product->barcode_series = $barcode;
                    $product->save();

                    $size->wms_product_id = $product->id;
                    $size->save();
                }
            }
        }
    }
}
