<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{

	public function items() {
		return $this->hasMany('App\Http\Models\ShipmentItem');
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

	public function user() {
		return $this->belongsTo('App\Http\Models\Shipper\User');
	}

	public function payment_mode() {
		return $this->belongsTo('App\Http\Models\PaymentMode');
	}

	public function receiving_sheet_shipment() {
		return $this->hasOne('App\Http\Models\ReceivingSheetShipment');
	}

	public function shipment_journey(){
	    return $this->hasMany('App\Http\Models\ShipmentsJourney')->orderBy('id', 'DESC');
    }

    public function shipment_payment_journey(){
	    return $this->hasMany('App\Http\Models\ShipmentsPaymentJourney')->orderBy('id', 'DESC');
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
}