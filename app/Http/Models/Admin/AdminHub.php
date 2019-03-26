<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AdminHub extends Model
{
    protected $primaryKey = ['admin_id', 'hub_id'];
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
}
