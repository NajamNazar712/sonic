<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admins\LeadTaggingController;
use App\Http\Models\Admin\AreaTerritory;
use App\Http\Models\Admin\AutoTagTerritory;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\Territory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LeadAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private  $rules = [
        'contact_person'      => ['required', 'max:255'],
        'phone_number'        => ['required', 'regex:/^[0][0-9]{3}-[0-9]{7}$/'],
        'email_address'       => ['required', 'unique:leads,email_address', 'email'],
        'city_id'             => ['required', 'integer', 'exists:cities,id'],
        'service_id'          => ['required', 'integer', 'exists:service_list,id'],
        'reference_person_id' => ['required', 'integer', 'exists:riders,id'],
        'territory_id'        => ['required', 'integer', 'exists:territories,id'],
        'territory_area_id'   => ['required', 'integer', 'exists:area_territories,id'],
        'brand'               => ['required', 'max:255'],
        'company'             => ['required', 'max:255'],
        'reference_id'        => ['required', 'integer', 'exists:lead_references,id'],

    ];

    public function index()
    {
        $leads = Lead::join('cities as c', 'c.id', '=', 'leads.city_id')
            ->join('service_list as sl', 'sl.id', '=', 'leads.service_id')
            // ->join('lead_references as lr', 'lr.id', '=', 'leads.reference_id')
            // ->join('territories as t', 't.id', '=', 'leads.territory_id')
            // ->join('area_territories as at', 'at.id', '=', 'leads.territory_area_id')
            ->join('lead_statuses as ls', 'ls.id', '=', 'leads.status_id')
            ->select('leads.id as id', 'leads.contact_person', 'sl.name as service_name', 'sl.id as service_id', 'ls.name as status', 'ls.id as status_id');

        if ($leads->exists()) {
            $leads = $leads->get();
            return response()->json(['status' => 0, 'leads' =>  $leads]);
        }
        return response()->json(['status' => 1, 'message', 'Leads not found']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $validate = Validator::make($request->all(), $this->rules);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }

        $auto_tag_territory = AutoTagTerritory::where('territory_id', $request->territory_id);
        $sales_person_id = null;
        if ($auto_tag_territory->exists()) {
            $auto_tag_territory = $auto_tag_territory->first();
            $sales_person_id = $auto_tag_territory->admin_id;
        }

        try {
            $new_lead = new Lead();
            $new_lead->contact_person = $request->contact_person;
            $new_lead->phone_number = $request->phone_number;
            $new_lead->email_address = $request->email_address;
            $new_lead->city_id = $request->city_id;
            $new_lead->requested_date = Carbon::now();
            $new_lead->sale_person_id = $sales_person_id;
            $new_lead->service_id = $request->service_id;
            $new_lead->reference_person_id = $request->reference_person_id;
            $new_lead->territory_id = $request->territory_id;
            $new_lead->territory_area_id = $request->territory_area_id;
            $new_lead->brand = $request->brand;
            $new_lead->company = $request->company;
            $new_lead->reference_id = $request->reference_id;
            $new_lead->status_id = 9;
            $new_lead->updated_by = Auth::id();
            $new_lead->save();

            if ($sales_person_id != null) {
            NotificationsController::send(113, $new_lead);
            }
            return response()->json(['status' => 0, 'success' => 'Lead added successfully']);
        } catch (\Exception $e) {

            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($id) {
            $lead = Lead::with('sales_person', 'reference_person', 'city', 'territory', 'service', 'area_territoy','lead_reference', 'status')->find($id);
            if ($lead) {
              
                $lead = [
                    'contact_person' =>  $lead->contact_person,
                    'phone_number' => $lead->phone_number,
                    'email_address' => $lead->email_address,
                    'brand' => $lead->brand,
                    'company' => $lead->company,
                    'service_name' => $lead->service->name,
                    'service_id' => $lead->service->id,
                    'sales_person_name' => $lead->sales_person->name,
                    'sales_person_id' => $lead->sales_person->id,
                    'reference_person_name' => $lead->reference_person->name,
                    'reference_person_id' => $lead->reference_person->id,
                    'city_name' => $lead->city->name,
                    'city_id' => $lead->city->id,
                    'territory_name' => $lead->territory->name,
                    'territory_id' => $lead->territory->id,
                    'area_name' => $lead->area_territoy->name,
                    'area_id' => $lead->area_territoy->id,
                    'lead_reference_id'=>$lead->lead_reference->id,
                    'lead_reference_name'=>$lead->lead_reference->name,
                    'status_id' => $lead->status->id,
                    'status_name' => $lead->status->name


                ];
                return response()->json(['status' => 1, 'lead' => $lead]);
            }
        }
        return response()->json(['status' => 1, 'error' =>  'Something went wrong!']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $lead_id = $id;
        if ($lead_id) {

            $lead = Lead::find($lead_id);
            if ($lead) {
                unset($this->rules['email_address']);
                $validate = Validator::make($request->all(), $this->rules);
                if ($validate->fails()) {
                    return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
                }
                $lead->contact_person = $request->contact_person;
                $lead->phone_number = $request->phone_number;
                $lead->email_address = $request->email_address;
                $lead->city_id = $request->city_id;
                $lead->service_id = $request->service_id;
                $lead->reference_person_id = $request->reference_person_id;
                $lead->territory_id = $request->territory_id;
                $lead->territory_area_id = $request->territory_area_id;
                $lead->brand = $request->brand;
                $lead->company = $request->company;
                $lead->reference_id = $request->reference_id;
                $lead->status_id = 15;
                $lead->save();
                // LeadTaggingController::auto_tagging($lead->id, Auth::id());
                return response()->json(['status' => 0, 'success' => 'Lead Edited successfully!']);
            }
            return response()->json(['status' => 1, 'error', 'Lead not found!']);
        }
        return response()->json(['status' => 1, 'error' =>  'Something went wrong!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function city_territories($city_id)
    {
        $city_id = $city_id;
        if ($city_id) {
            $territories = Territory::where('city_id', $city_id)->where('territory_status', 1);
            if ($territories->exists()) {
                $territories = $territories->get();
                return response()->json(['status' => 0, 'territories' =>  $territories]);
            } else {
                return response()->json(['status' => 0, 'error' =>  $territories]);
            }
        }
        return response()->json(['status' => 1, 'message', 'Territories not found']);
    }

    public function territory_areas($territory_id)
    {
        $territory_id = $territory_id;
        if ($territory_id) {
            $territory_areas = AreaTerritory::where('territory_id', $territory_id)->where('area_territory_status', 1);
            if ($territory_areas->exists()) {
                $territory_areas = $territory_areas->get();
                return response()->json(['status' => 0, 'territory_areas' =>  $territory_areas]);
            } else {
                return response()->json(['status' => 1, 'message', 'Territory Areas not found']);
            }
        }
        return response()->json(['status' => 1, 'error' => 'Something went wrong!']);
    }
}
