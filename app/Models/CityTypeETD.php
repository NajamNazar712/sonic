<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CityTypeETD extends Model
{
    use HasFactory;
    
    use SoftDeletes;

    protected $table = 'city_type_etds';

    protected $fillable = ['name', 'status'];

    
    protected $casts = [
        'status' => 'boolean',
    ];

    // Accessor for readable status
    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }
}
