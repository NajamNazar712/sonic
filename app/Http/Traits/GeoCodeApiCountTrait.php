<?php

namespace App\Http\Traits;

use App\Models\GeoCodeApiCount;

trait GeoCodeApiCountTrait
{
  function geo_code_api_count($count)
  {
      if ($count > 0) {
          $record = GeoCodeApiCount::first();
          if ($record) {
              $record->increment('api_count', $count);
          } else {
              GeoCodeApiCount::create(['api_count' => $count]);
          }
      }
  }
}