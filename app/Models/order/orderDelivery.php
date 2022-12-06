<?php

namespace App\Models\order;

use App\Models\data\city;
use App\Models\data\country;
use App\Models\data\region;
use App\Models\users\customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class orderDelivery extends Model
{
    use HasFactory;
    public $table  = 'orderdelivery';
    public  $timestamps  =false;
    public $fillable = [
        "id", "country", "city", "region","address", "latitude", "longitude", "order_id", "sp_received", "customer_received", "status", "receiver_mobile", "receiver_name", "qr_code","from_location"
    ];



    static function store($item){
        $orderDelivery =  self::create([
            "country"=>request()->country,
             "city"=>request()->city,
             "region"=>request()->region,
             "latitude"=>request()->latitude,
             "longitude"=>request()->longitude,
             "address"=>request()->address ?? '',
             "order_id"=>$item->id,
             "receiver_mobile"=>request()->receiver_mobile,
             "receiver_name"=>request()->receiver_name,
             "qr_code"=>uniqid('#'.$item->id),
             'from_location'=>customer::find(Auth('sanctum')->user()->id)->Location->id
            ]);

        }




public function get(){
$this->country = country::find($this->country)->country_name ?? null;
$this->city = city::find($this->city)->city_name ?? null;
$this->region = region::find($this->region)->region_name?? null;
return $this;

}


public function order(){

     return $this->belongsTo(order::class , 'order_id');

}



}
