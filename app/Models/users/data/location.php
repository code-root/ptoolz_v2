<?php

namespace App\Models\users\data;

use App\Models\data\city;
use App\Models\data\region;
use App\Models\data\country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class location extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = 'location';
    public $fillable = [
       "id",
       "country",
       "city",
       "region",
       "address",
       "latitude",
       "longitude",
       "user_key"
    ];
 public function country(){
    return $this->belongsTo(country::class , 'country');
 }
 public function city(){
    return $this->belongsTo(city::class , 'city');
 }
 public function region(){
    return $this->belongsTo(region::class , 'region');
 }

}
