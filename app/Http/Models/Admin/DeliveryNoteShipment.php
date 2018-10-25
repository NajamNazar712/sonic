<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DeliveryNoteShipment extends Model
{
	protected $primaryKey = ['delivery_note_id', 'shipment_id'];
	public $incrementing = FALSE;
	public $timestamps = FALSE;
	protected $fillable = [
		'delivery_note_id','shipment_id','status','call_verification','notification','rider_information'
	];

	protected function setKeysForSaveQuery(Builder $query) {
		$keys = $this->getKeyName();

		if (!is_array($keys)) {
			return parent::setKeysForSaveQuery($query);
		}

		foreach ($keys as $keyName) {
			$query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
		}

		return $query;
	}

	protected function getKeyForSaveQuery($keyName = null) {
		if (is_null($keyName)){
			$keyName = $this->getKeyName();
		}

		if (isset($this->original[$keyName])) {
			return $this->original[$keyName];
		}

		return $this->getAttribute($keyName);
	}

	public function shipment() {
		return $this->belongsTo('App\Http\Models\Shipment');
	}
}
