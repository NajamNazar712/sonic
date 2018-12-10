<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DeliveryNoteStationDepositNote extends Model
{
    protected $table = 'delivery_note_station_deposit_notes';
    protected $primaryKey = ['delivery_note_id', 'shipment_id'];
	public $incrementing = FALSE;
	public $timestamps = FALSE;
    protected $fillable = [ 'station_deposit_note_id','delivery_note_id' ];

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

	public function station_deposit_note() {
        return $this->hasOne('App\Http\Models\Admin\StationDepositNote');
    }
}
