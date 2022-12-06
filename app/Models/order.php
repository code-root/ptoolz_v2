<?php

namespace App\Models\order;

use App\Models\offer\offer;
use App\Models\data\category;
use App\Models\data\ordertype;
use App\Models\users\customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class order extends Model
{
    use HasFactory;
    public  $timestamps  = false;
    // public static function boot()
    // {
    //     parent::boot();
    //     static::retrieved(function ($item) {
    //         $item->category_name = category::find($item->category_id)->department_name;
    //         $item->order_type_name = ordertype::find($item->order_type_id)->name;
    //     });
    // }

    public $fillable = [
        "id",
        'code',
        "category_id",
        "order_type_id",
        "client_id",
        "title",
        "order_id",
        "description",
        "status",
        "active_time",
        "created_at"
    ];

    protected $appends = ['category_name' , 'order_type_name'];


    static function store($item)
    {
        $order =  self::create([
            "category_id" => $item->categoryId,
            "order_type_id" => $item->orderTypeId,
            "client_id" => auth("sanctum")->user()->id,
            "title" => request()->title,
            "description" => request()->description,
            "status" => 1,
            "order_id" => $item->id,
            'code' => random_int(0, 1000)
        ]);
        return $order;
    }


    public function delivery()
    {
        return $this->hasOne(orderDelivery::class, 'order_id');
    }


    public function cameraCart()
    {
        return $this->hasMany(cameracart::class, 'order_id');
    }
    // public function accessory(){
    // return $this->hasMany(cameracart::class , 'order_id')->where('type' , 2 );
    // }

    public function offers()
    {
        return $this->hasMany(offer::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(customer::class, 'client_id');
    }

    public function getCategoryNameAttribute($value)
{
    return category::find($this->category_id)->department_name;
}
    public function getOrderTypeNameAttribute($value)
{
    return ordertype::find($this->order_type_id)->name;
}
}
