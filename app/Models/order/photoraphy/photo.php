<?php

namespace App\Models\order\photoraphy;

use App\Models\data\city;
use App\Models\data\camera;
use App\Models\data\region;
use App\Models\order\order;
use App\Models\data\country;
use App\Models\data\occasion;
use Illuminate\Validation\Rule;
use App\Models\order\cameracart;
use App\Models\data\cameraaccessory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class photo extends Model
{
    use HasFactory;
    public $table  = 'orderphoto';
    public  $timestamps  = false;
    public $categoryId = 3;
    public $orderTypeId = 7;
    public $fillable = [
        "id",
        "occasion_id",
        "occasion_name",
        "duration_days",
        "duration_hours",
        "duration_minutes",
        "montage",
        "lighting",
        'country',
        "city",
        "region",
        "latitude",
        "longitude",
        "address",
        "occassion_date",
        "occassion_time"
    ];



    public static function boot()
    {

        parent::boot();

        static::created(function ($item) {

            $order = order::store($item);
            $order->code =  dechex($order->id);
            $order->save();

            if (isset(request()->cart)) {
                $cart  = json_decode(request()->cart);
                foreach ($cart as $element) {
                    $parent = $element->type == 1 ? Null : $element->parent_id;
                    cameracart::create([
                        'type' => $element->type,
                        'number' => $element->number,
                        'item_id' => $element->item_id,
                        'parent_id' => $parent,
                        'order_id' => $order->id
                    ]);
                }
            }
        });
    }




    public function store()
    {


        $validator = Validator::make(
            request()->all(),
            [
                "occasion_id" => 'required',
                "duration_days" => 'required',
                "duration_hours" => 'required',
                "duration_minutes" => 'required',
                "montage" => 'required',
                "lighting" => 'required',
                "country" => 'required',
                "city" => 'required',
                "region" => 'required',
                "latitude" => 'required',
                "longitude" => 'required',
                "address" => 'required',
                "occassion_date" => 'required',
                "occassion_time" => 'required',
                "title" => 'required',
                "description" => 'required',
                'occasion_name'=>'required_if:occasion_id,0',

            ],
            validationMessages()
        );

        if ($validator->fails())
            return apiresponse(false, 200, $validator->errors()->first());

            $occasion_name  = request()->occasion_id == 0 ? request()->occasion_name  : occasion::find(request()->occasion_id)->name ;

          $photo = $this->create([
             "occasion_id" => request()->occasion_id,
            "duration_days" => request()->duration_days,
            "duration_hours" => request()->duration_hours,
            "duration_minutes" => request()->duration_minutes,
            "montage" => request()->montage,
            "lighting" => request()->lighting,
            "country" => request()->country,
            "city" => request()->city,
            "region" => request()->region,
            "latitude" => request()->latitude,
            "address" => request()->address,
            "occasion_name"=>$occasion_name,
            "longitude" => request()->longitude,
            "occassion_date" => request()->occassion_date,
            "occassion_time" => request()->occassion_time

        ]);


        return apiresponse(true, 200, __("order.order_stored"));
    }














    public function edit(order $order)
    {
        $model = $this->find($order->order_id);

        $validator = Validator::make(
            request()->all(),
            [
                "occasion_id" => 'required',
                "duration_days" => 'required',
                "duration_hours" => 'required',
                "duration_minutes" => 'required',
                "montage" => 'required',
                "lighting" => 'required',
                "country" => 'required',
                "city" => 'required',
                "region" => 'required',
                "latitude" => 'required',
                "longitude" => 'required',
                "address" => 'required',
                "occassion_date" => 'required',
                "occassion_time" => 'required',
                "title" => 'required',
                "description" => 'required',
                'occasion_name'=>'required_if:occasion_id,0',

            ],
            validationMessages()
        );

        if ($validator->fails())
            return apiresponse(false, 200, $validator->errors()->first());

            $occasion_name  = request()->occasion_id == 0 ? request()->occasion_name  : occasion::find(request()->occasion_id)->name ;

          $model->update([
             "occasion_id" => request()->occasion_id,
            "duration_days" => request()->duration_days,
            "duration_hours" => request()->duration_hours,
            "duration_minutes" => request()->duration_minutes,
            "montage" => request()->montage,
            "lighting" => request()->lighting,
            "country" => request()->country,
            "city" => request()->city,
            "region" => request()->region,
            "latitude" => request()->latitude,
            "address" => request()->address,
            "occasion_name"=>$occasion_name,
            "longitude" => request()->longitude,
            "occassion_date" => request()->occassion_date,
            "occassion_time" => request()->occassion_time

        ]);


        // update order

        unset($order->category_name);
        unset($order->order_type_name);
         $order->title = request()->title;
         $order->description = request()->description;
          $order->save();

          //  update Cart

         if (isset(request()->cart)) {
            $cart  = json_decode(request()->cart);
            foreach ($cart as $element) {
                $parent = $element->type == 1 ? Null : $element->parent_id;
                cameracart::create([
                    'type' => $element->type,
                    'number' => $element->number,
                    'item_id' => $element->item_id,
                    'parent_id' => $parent,
                    'order_id' => $order->id
                ]);
            }
        }

        return apiresponse(true, 200, "updated successfully");
    }
















    public function get(order $mainOrder)
    {
        $this->main_order = order::find($mainOrder->id);
        $this->country = country::find($this->country)->country_name ?? null;
        $this->city = city::find($this->city)->city_name ?? null;
        $this->region = region::find($this->region)->region_name ?? null;
        $cameras = [];
        $accessories = [];

        $cart = [];
        foreach ($mainOrder->cameraCart as $item) {
            if ($item->type == 1) {
                $camera = $item->cameras;
                $camera->number = $item->number;
                $cart_accessories = [];
                foreach ($item->cameras->cartaccessories as $accessory) {
                    if($accessory->order_id == $item->order_id)
                    {
                        $accessory_item = cameraaccessory::find($accessory->item_id);
                        $accessory_item->number = $accessory->number;
                        array_push($cart_accessories, $accessory_item);
                    }
                }
                $camera->accessories = $cart_accessories;
                unset($camera->cartaccessories);
                array_push($cart, $camera);
            }
        }
        $this->cart = $cart;
        return $this;
    }

    public function get_update(order $mainOrder)
    {
        $this->country = country::find($this->country)->country_name ?? null;
        $this->city = city::find($this->city)->city_name ?? null;
        $this->region = region::find($this->region)->region_name ?? null;
        $this->main_order = order::find($mainOrder->id);

        $cameras = camera::all();
        $accessories  = cameraaccessory::all();

        foreach($cameras as $camera)
        {
            $cart_check = cart_item_selected($camera->id , $mainOrder->id , 1);
            $camera->selected =$cart_check['selected'] ;
            $camera->cart =$cart_check['pivot'] ;
        }
        foreach($accessories as $accessory){
            $cart_check = cart_item_selected($accessory->id , $mainOrder->id , 2);
            $accessory->selected =$cart_check['selected'] ;
            $accessory->cart =$cart_check['pivot'] ;
        }


        $this->cameras  = $cameras ;
        $this->accessories  = $accessories ;

        return $this;

    }



}
