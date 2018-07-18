<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\PackagingMaterialStockHead;
use App\Http\Models\Admin\PackagingMaterialStockHub;
use App\Http\Models\Admin\PackagingStockHistory;
use App\Http\Models\City;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class AdminPackagingMaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function packaging_index(){
        $packaging = PackagingMaterialStockHead::latest()->first();
        return view('admin.materials.flyers.index')->with('packaging',$packaging);
    }
    public function packaging_list(Request $request){
        $packaging = PackagingStockHistory::leftjoin('cities','cities.id','=','packaging_stock_histories.hub_id')
            ->join('admins as ad','ad.id','=','packaging_stock_histories.admin_id')
            ->select(['packaging_stock_histories.id as psh_id','packaging_stock_histories.reference_number','packaging_stock_histories.entry_type','packaging_stock_histories.created_at','packaging_stock_histories.small_flyers','packaging_stock_histories.medium_flyers','packaging_stock_histories.large_flyers','packaging_stock_histories.boxes','ad.name as admin','cities.name as hub']);
        return Datatables::of($packaging)

            ->editColumn('created_at', function ($packaging) {
                return $packaging->created_at ? with(new Carbon($packaging->created_at))->format('d/m/Y h:i:s A') : '';
            })
            ->editColumn('entry_type',function($packaging){
                if($packaging->entry_type == 0){
                    return "Inbound";
                }
                else if($packaging->entry_type == 1){
                    return "Outbound";
                }
            })
            ->make(true);
    }
    public function add_stock(Request $request){
        $reference_number = $request->invoice_number;
        $sm_quantity = ($request->add_stock_smflyer != null)? $request->add_stock_smflyer:0;
        $md_quantity = ($request->add_stock_mdflyer != null)? $request->add_stock_mdflyer:0;
        $lg_quantity = ($request->add_stock_lgflyer != null)? $request->add_stock_lgflyer:0;
        $box_quantity = ($request->add_stock_boxes != null)? $request->add_stock_boxes:0;
        if($reference_number != null){
            $packaging = PackagingMaterialStockHead::latest()->first();
            if($packaging){
                $small = $packaging->small_flyers;
                $medium = $packaging->medium_flyers;
                $large = $packaging->large_flyers;
                $box = $packaging->boxes;
                $small += $sm_quantity;
                $medium += $md_quantity;
                $large += $lg_quantity;
                $box += $box_quantity;
              $packaging_head =  PackagingMaterialStockHead::create([
                   'small_flyers'=>$small,
                   'medium_flyers'=>$medium,
                   'large_flyers'=>$large,
                   'boxes'=>$box
                ]);
                if($packaging_head){
                    PackagingStockHistory::create([
                        'admin_id'=>Auth::id(),
                        'small_flyers'=>$sm_quantity,
                        'medium_flyers'=>$md_quantity,
                        'large_flyers'=>$lg_quantity,
                        'boxes'=>$box_quantity,
                        'entry_type'=>0,
                        'reference_number'=>$reference_number
                    ]);
                }
                return redirect()->back()->with('success','Stock added successfully!');
            }else{
                return redirect()->back()->with('error','No previous record found in database!');

            }
        }else{
            return redirect()->back()->with('error','No invoice number entered!');
        }
    }
    public function fetch_cities(Request $request){
        $cities = City::where(['hub'=>1,'status'=>1])->select('id','name')->get();
        return response()->json(['status'=>1,'cities'=>$cities]);

    }
    public function send_stock(Request $request){
//        return $request;
        $small = 0; $medium = 0; $large = 0; $box = 0;
        $sm_quantity = ($request->send_stock_smflyer != null)? $request->send_stock_smflyer:0;
        $md_quantity = ($request->send_stock_mdflyer != null)? $request->send_stock_mdflyer:0;
        $lg_quantity = ($request->send_stock_lgflyer != null)? $request->send_stock_lgflyer:0;
        $box_quantity = ($request->send_stock_boxes != null)? $request->send_stock_boxes:0;
        $hub_id = $request->city_select;
        $reference_number = $request->invoice_number;
        if($reference_number != null){
            $packaging = PackagingMaterialStockHub::where('hub_id',$hub_id);
            if($packaging->exists()){
                $packaging = $packaging->first();
                $small = $packaging->small_flyers;
                $medium = $packaging->medium_flyers;
                $large = $packaging->large_flyers;
                $box = $packaging->boxes;
                $result = $this->sub_head_stock($sm_quantity,$md_quantity,$lg_quantity,$box_quantity);

                if($result) {
                    $small += $sm_quantity;
                    $medium += $md_quantity;
                    $large += $lg_quantity;
                    $box += $box_quantity;
                    $packaging_hub = PackagingMaterialStockHub::where('hub_id', $hub_id)->update([
                        'small_flyers' => $small,
                        'medium_flyers' => $medium,
                        'large_flyers' => $large,
                        'boxes' => $box
                    ]);
                    if ($packaging_hub) {
                        PackagingStockHistory::create([
                            'admin_id' => Auth::id(),
                            'small_flyers' => $sm_quantity,
                            'medium_flyers' => $md_quantity,
                            'large_flyers' => $lg_quantity,
                            'boxes' => $box_quantity,
                            'entry_type' => 1,
                            'hub_id' => $hub_id,
                            'reference_number' => $reference_number
                        ]);
                    }
                    return redirect()->back()->with('success', 'Stock added successfully!');
                }else{
                    return redirect()->back()->with('error','Stock looks short, check again!');

                }
            }else{
                $result = $this->sub_head_stock($sm_quantity,$md_quantity,$lg_quantity,$box_quantity);
                if($result) {


                    $packaging_hub = PackagingMaterialStockHub::create([
                        'hub_id' => $hub_id,
                        'small_flyers' => $sm_quantity,
                        'medium_flyers' => $md_quantity,
                        'large_flyers' => $lg_quantity,
                        'boxes' => $box_quantity
                    ]);
                    if ($packaging_hub) {
                        PackagingStockHistory::create([
                            'admin_id' => Auth::id(),
                            'small_flyers' => $sm_quantity,
                            'medium_flyers' => $md_quantity,
                            'large_flyers' => $lg_quantity,
                            'boxes' => $box_quantity,
                            'entry_type' => 1,
                            'hub_id' => $hub_id,
                            'reference_number' => $reference_number
                        ]);
                    }
                    return redirect()->back()->with('success', 'Stock added successfully!');
                }else{
                    return redirect()->back()->with('error','Stock looks short, check again!');
                }
            }
        }else{
            return redirect()->back()->with('error','No invoice number entered!');
        }
    }
    protected function sub_head_stock($small,$medium,$large,$box){
        $head_stocks = PackagingMaterialStockHead::latest()->first();
        $small_flyers = $head_stocks->small_flyers;
        $medium_flyers = $head_stocks->medium_flyers;
        $large_flyers = $head_stocks->large_flyers;
        $boxes = $head_stocks->boxes;
        if($small < $small_flyers && $medium < $medium_flyers && $large < $large_flyers && $box < $boxes){
            $small_flyers -= $small;
            $medium_flyers -= $medium;
            $large_flyers -= $large;
            $boxes -= $box;
            $packaging_head =  PackagingMaterialStockHead::create([
                'small_flyers'=>$small_flyers,
                'medium_flyers'=>$medium_flyers,
                'large_flyers'=>$large_flyers,
                'boxes'=>$boxes
            ]);
            if($packaging_head){
                return true;
            }
        }else{
            return false;
        }
    }
}
