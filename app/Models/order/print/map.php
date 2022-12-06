<?php

namespace App\Models\order\print;

use App\Models\order\order;
use App\Models\data\paperSize;
use App\Models\data\paperType;
use App\Models\data\printingColor;
use App\Models\data\printingSide;
use App\Models\order\orderDelivery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class map extends Model
{
    use HasFactory;
    public $table  = 'ordermap';
    public  $timestamps  = false;
    public $categoryId = 1;
    public $orderTypeId = 2;
    public $fillable = [
        "id",
        "file",
        "paper_size",
        "paper_type",
        "delivery",
        "printing_color",
        "printing_side",
        "number",
    ];


    public static function boot()
    {

        parent::boot();

        static::created(function ($item) {

            $order = order::store($item);
            $order->code =  dechex($order->id);
            $order->save();
            if (request()->delivery == 1)
                orderDelivery::store($order);
            //  setOrderDelivery($order->id , $order->code);

        });

        // static::creating(function($item) {
        //     \Log::info('Item Creating Event:'.$item);
        // });

    }


    public function store()
    {

        $validator = Validator::make(
            request()->all(),

            [
                "file" => 'required',
                "title" => 'required',
                "description" => 'required',
                "paper_size" => 'required',
                "paper_type" => 'required',
                "delivery" => 'required',
                "printing_color" => 'required',
                "printing_side" => 'required',
                "number" => 'required',
                "country" => "required_if:delivery,1",
                'city' => 'required_if:delivery,1',
                'region' => 'required_if:delivery,1',
                'latitude' => 'required_if:delivery,1',
                'longitude' => 'required_if:delivery,1',
                'receiver_mobile' => 'required_if:delivery,1',
                'receiver_name' => 'required_if:delivery,1',
                'address' => 'required_if:delivery,1'
            ],
            validationMessages()
        );

        if ($validator->fails())
            return apiresponse(false, 200, $validator->errors()->first());

        $file = handleFile(env('IMAGE_VALID_EXTENSIONS'), 'file');

        if (!$file['valid'])
            return apiresponse(false, 200, "select a valid file");


        $path =  Storage::disk('order')->put('map/' . $file['fileName'], $file['content']);

        $delivery = isset(request()->delivery) ? request()->delivery : 0;
        $map = $this->create([
            "file" => $file['fileName'],
            "paper_size" => request()->paper_size,
            "paper_type" => request()->paper_type,
            "delivery" => $delivery,
            "printing_color" => request()->printing_color,
            "printing_side" => request()->printing_side,
            "number" => request()->number,

        ]);


        return apiresponse(true, 200, __("order.order_stored"));
    }




    public function edit(order $order)
    {
        $model = $this->find($order->order_id);
        $delivery = $model->delivery;

        $validator = Validator::make(
            request()->all(),

            [
                "title" => 'required',
                "description" => 'required',
                "paper_size" => 'required',
                "paper_type" => 'required',
                "delivery" => 'required',
                "printing_color" => 'required',
                "printing_side" => 'required',
                "number" => 'required',
                "country" => "required_if:delivery,1",
                'city' => 'required_if:delivery,1',
                'region' => 'required_if:delivery,1',
                'latitude' => 'required_if:delivery,1',
                'longitude' => 'required_if:delivery,1',
                'receiver_mobile' => 'required_if:delivery,1',
                'receiver_name' => 'required_if:delivery,1',
                'address' => 'required_if:delivery,1'
            ],
            validationMessages()
        );

        if ($validator->fails())
            return apiresponse(false, 200, $validator->errors()->first());


        if (isset(request()->file)) {
            $file = handleFile(env('IMAGE_VALID_EXTENSIONS'), 'file');

            if (!$file['valid'])
                return apiresponse(false, 200, "select a valid file");

            $path =  Storage::disk('order')->put('map/' . $file['fileName'], $file['content']);
            $file = $file['fileName'];
        } else {

            $file = $this->file;
        }



        $model->update([
            "file" => $file,
            "paper_size" => request()->paper_size,
            "paper_type" => request()->paper_type,
            "delivery" => request()->delivery ?? 0,
            "printing_color" => request()->printing_color,
            "printing_side" => request()->printing_side,
            "number" => request()->number,
        ]);

        //  update main order


        unset($order->category_name);
        unset($order->order_type_name);
        $order->title = request()->title;
        $order->description = request()->description;
        $order->save();
        //  update delivery

        if ($delivery == 0) {  // no delivery before

            if (request()->delivery == 1) {
                orderDelivery::store($order);
            }
        } elseif ($delivery == 1)   // has delivery
        {

            if (request()->delivery == 0) {
                orderDelivery::find($order->delivery->id)->delete();
            }
            if ($order->delivery ==  NULL)
                orderDelivery::store($order);

            $order->delivery->update([
                "country" => request()->country,
                "city" => request()->city,
                "region" => request()->region,
                "latitude" => request()->latitude,
                "longitude" => request()->longitude,
                "address" => request()->address ?? '',
                "receiver_mobile" => request()->receiver_mobile,
                "receiver_name" => request()->receiver_name,
            ]);
        }
        return apiresponse(true, 200, "updated successfully");
    }


















    public function get(order $mainOrder)
    {
        $this->main_order = order::find($mainOrder->id);
        $this->paper_size = paperSize::find($this->paper_size) ?? null;
        $this->paper_type = paperType::find($this->paper_type) ?? null;
        $this->printing_color = printingColor::find($this->printing_color) ?? null;
        $this->printing_side = printingSide::find($this->printing_side) ?? null;
        $this->delivey_data = $mainOrder->delivery == null ? null : $mainOrder->delivery->get();
        return $this;
    }

    public function size()
    {
        return $this->belongsTo(paperSize::class, 'paper_size');
    }
    public function type()
    {
        return $this->belongsTo(paperType::class, 'paper_type');
    }

    public function color()
    {
        return $this->belongsTo(printingColor::class, 'printing_color');
    }
    public function side()
    {
        return $this->belongsTo(printingSide::class, 'printing_side');
    }
}
