<?php

use App\Http\Models\PackagingMaterialRequestDetail;
use App\Http\Models\Shipper\User;
use App\Http\Models\WMS\WmsProduct;
use App\Http\Models\WMS\WmsProductCategory;
use App\Http\Models\PackagingMaterialTypeSizes;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Warehouse\Warehouse;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\City;
use App\Http\Models\WarehouseStock;
use Carbon\Carbon;
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
        $setting = GlobalSettings::where('type', 'packaging_material');
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
                    $warehouse_stocks = WarehouseStock::where('type_size_id', $size->id);
                    if($warehouse_stocks->exists()){
                        $warehouse_stocks = $warehouse_stocks->get();
                        foreach ($warehouse_stocks as $warehouse_stock){
                            $warehouse_stock->wms_product_id = $product->id;
                            $warehouse_stock->save();
                        }
                    }
                    $request_details = PackagingMaterialRequestDetail::where('type_size_id', $size->id);
                    if($request_details->exists()){
                        $request_details = $request_details->get();
                        foreach ($request_details as $request_detail){
                            $request_detail->wms_product_id = $product->id;
                            $request_detail->save();
                        }
                    }
                }
            }
            $warehouses = Warehouse::where('master_type', 0)->where('status', 1)->where('pickup_address_id', NULL);
            if($warehouses->exists()){
                $warehouses = $warehouses->get();
                $user = User::find($setting->setting_value);
                foreach ($warehouses as $warehouse){
//                if($warehouse->hub_id != 202){
                    $hub = City::find($warehouse->hub_id);
                    $pickup_address = 'Trax Warehouse ' . $hub->name;
                    $user_shipping_info = new UserShippingInfo();
                    $user_shipping_info->user_id = $user->id;
                    $user_shipping_info->pickup_address = $pickup_address;
                    $user_shipping_info->poc = 'Trax Office';
                    $user_shipping_info->phone = '0304-1111232';
                    $user_shipping_info->email = 'packaging_material@trax.pk';
                    $user_shipping_info->city_id = $warehouse->hub_id;
                    $user_shipping_info->hidden = 0;
                    $user_shipping_info->default_address = 0;
                    $user_shipping_info->status = 1;
                    $user_shipping_info->warehouse = 1;
                    $user_shipping_info->save();

                    $warehouse->pickup_address_id = $user_shipping_info->id;
                    $warehouse->save();
//                }
                }
            }
        }
    }
}
