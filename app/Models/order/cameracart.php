<?php

namespace App\Models\order;

use App\Models\data\camera;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cameracart extends Model
{
    use HasFactory;
    public $table  = 'cameracart';
    public  $timestamps  =false;
    public $fillable = [
        "id", "type", "item_id", "parent_id" , "number", "order_id"
    ];

    static function store(){
        self::create([
            "type",
            "item_d",
            "number",
            "order_id",
            'parent_id'
        ]);
    }


  public function cameras(){
    return $this->belongsTo(camera::class , 'item_id');
  }
}
;
