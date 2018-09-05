<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PickupRequestAssignedShipment extends Model
{
	protected $primaryKey = ['pickup_request_id', 'shipment_id'];
	public $incrementing = FALSE;
	public $timestamps = FALSE;

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

	public function pickup_request() {
		return $this->belongsTo('App\Http\Models\PickupRequest');
	}
}