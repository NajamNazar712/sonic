<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = ['booking_type_id', 'shipper_status_id', 'consignee_status_id'];
	public function items() {
		return $this->hasMany('App\Http\Models\ShipmentItem');
	}
    public function distribution_products() {
        return $this->hasMany('App\Http\Models\ShipmentDistributionProduct','shipment_id');
    }
    public function shipment_pieces() {
        return $this->hasMany('App\Http\Models\ShipmentPiece');
    }

	public function booking_type() {
		return $this->belongsTo('App\Http\Models\BookingType');
	}

	public function shipping_mode() {
		return $this->belongsTo('App\Http\Models\ShippingMode');
	}

	public function pickup_address() {
		return $this->belongsTo('App\Http\Models\Shipper\UserShippingInfo');
	}

	public function consignee_city() {
		return $this->belongsTo('App\Http\Models\City', 'consignee_city_id', 'id');
	}
    public function destination_city() {
        return $this->belongsTo('App\Http\Models\City', 'consignee_city_id', 'id');
    }

	public function user() {
		return $this->belongsTo('App\Http\Models\Shipper\User');
	}

	public function payment_mode() {
		return $this->belongsTo('App\Http\Models\PaymentMode');
	}

	public function receiving_sheet_shipment() {
		return $this->hasOne('App\Http\Models\ReceivingSheetShipment');
	}

    public function retail_shipment()
    {
        return $this->hasOne(RetailShipment::class, 'shipment_id');
    }
	public function shipment_journey(){
	    return $this->hasMany('App\Http\Models\ShipmentsJourney')->orderBy('id', 'DESC');
    }

	public function latest_shipment_journey(){
	    return $this->hasOne('App\Http\Models\ShipmentsJourney')->orderBy('id', 'DESC')->orderBy('updated_at','desc');
    }

    public function shipment_payment_journey(){
        if($this->shipment_type == 2) {
            return $this->hasMany('App\Http\Models\RetailShipmentsPaymentJourney')->orderBy('id', 'DESC');
        }
        else{
            return $this->hasMany('App\Http\Models\ShipmentsPaymentJourney')->orderBy('id', 'DESC');
        }
    }

    public function shipment_pickup_journey(){
	    return $this->hasMany('App\Http\Models\ShipmentsPickupJourney')->orderBy('id', 'DESC');
    }

    public function status_shipper() {
    	return $this->belongsTo('App\Http\Models\ShipmentStatus', 'shipper_status_id', 'id');
    }

    public function status_consignee() {
    	return $this->belongsTo('App\Http\Models\ShipmentStatus', 'consignee_status_id', 'id');
    }

    public function payment_status() {
    	return $this->belongsTo('App\Http\Models\ShipmentPaymentStatus', 'payment_status_id', 'id');
    }

    public function misrouted_history(){
        return $this->hasMany('App\Http\Models\MisroutedHistory');
    }

    public function done_payment_shipments() {
    	return $this->hasMany('App\Http\Models\DonePaymentShipment');
    }

    public function charges_mode() {
		return $this->belongsTo('App\Http\Models\ChargesMode');
	}

	public function intercept_history(){
        return $this->hasOne('App\Http\Models\InterceptReBookRequestHistory');
    }

    public function weight_change_log() {
        return $this->hasMany('App\Http\Models\Admin\ChangeShipmentWeightLog')->orderBy('id', 'DESC');
    }

    public function amount_change_log() {
        return $this->hasMany('App\Http\Models\Admin\ChangeShipmentAmountLog')->orderBy('id', 'DESC');
    }
    public function open_box_journey(){
	    return $this->hasMany('App\Http\Models\ShipmentOpenBoxJourney')->orderBy('id', 'DESC');
    }
    public function invoice() {
        return $this->hasMany('App\Http\Models\ShipmentInvoice');
	}
	public function shipments_v2_pickup_journeys(){
	    return $this->hasMany('App\Http\Models\V2Pickup\ShipmentsV2PickupJourney')->orderBy('id', 'DESC');
	}
	public function handover_shipments_journeys(){
	    return $this->hasMany('App\Http\Models\Handover\HandoverShipmentsJourney')->orderBy('id', 'DESC');
    }

    public function order_date(){
        return $this->hasOne('App\Http\Models\ShipmentOrderDate', 'shipment_id', 'id');
    }
    public function open_parcel_shipment(){
        return $this->hasOne('App\Http\Models\OpenParcelHistory');
    }

    public function business_category() {
        return $this->belongsTo('App\Http\Models\BusinessCategory');
    }

    public function retail() {
        return $this->belongsTo('App\Http\Models\Admin\Retail\RetailShipment', 'id', 'shipment_id');
    }

 public function ftl() {
        return $this->hasOne('App\Http\Models\Admin\FtlRequest');
    }
    public function shipment_detail() {
		return $this->hasOne('App\Http\Models\ShipmentDetail');
	}
	public function return_address() {
        return $this->belongsTo('App\Http\Models\Shipper\UserShippingInfo', 'return_address_id');
    }
	public function other_retail() {
        return $this->belongsTo('App\Http\Models\Admin\Retail\OtherRetailShipment', 'id', 'shipment_id');
    }
    public function pod_image() {
		return $this->hasOne('App\Http\Models\Admin\PODImage');
	}

    public function packaging_material() {
        return $this->belongsTo('App\Http\Models\PackagingMaterialRequest', 'id', 'shipment_id');
    }
    
    public function shipment_assign_agent() {
        return $this->hasOne('App\Http\Models\ShipmentAssignAgent');
    }
    public function faf_charges_data() {
        return $this->belongsTo('App\ShipmentAdditionalCharges', 'id', 'shipment_id');
    }
    public function bookingChannel()
    {
        return $this->hasOne('App\Models\BookingChannel');
    }




}