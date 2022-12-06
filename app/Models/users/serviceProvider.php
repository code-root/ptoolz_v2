<?php

namespace App\Models\users;

use App\Models\offer\offer;
use Laravel\Sanctum\HasApiTokens;
use App\Models\users\data\location;
use App\Models\users\data\emailCode;
use App\Models\users\data\portfolio;
use Illuminate\Support\Facades\Hash;
use App\Models\users\data\mobileCode;
use App\Models\users\data\experiences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use App\Models\order\eductional\reference;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\order\eductional\academicpermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class serviceProvider  extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $table = 'serviceprovider';
    public $timestamps = false;
    public $fillable =
    [
        "id",
         "username",
         "email",
         "full_name",
         "mobile",
         "password",
         "user_key",
         "activity",
         "account_type_id",
         "category_id",
    ];
    protected $hidden = ['password'];

    public static function boot() {

	    parent::boot();
	    static::created(function($item) {
            $user_key = 'sp'.$item->id;
            $item->user_key = $user_key;
            $item->save();
            $emailCode =random_int(10,99).rand(10,99);
            $mobileCode =1234;
           location::create([
            'country'=>getLocation()->country_id,
            'city'=>getLocation()->city_id,
            'region'=>getLocation()->region_id,
            'latitude'=>getLocation()->latitude,
            'longitude'=>getLocation()->longitude,
            'user_key'=>$user_key

           ]);
          emailcode::create([
            'code'=>$emailCode,
            'type'=>'1',
            'user_key'=>$user_key

  ]);
          mobilecode::create([
            'code'=>$mobileCode,
            'type'=>'1',
            'user_key'=>$user_key

        ]);
        account_verification($item->email , $emailCode);

        });
    //     static::retrieved(function($item) {
    //         $item->avatar = env('PROFILE_IMG_URL').$item->avatar;
    //    } );
	    // static::creating(function($item) {
	    //     \Log::info('Item Creating Event:'.$item);
	    // });

	}


    public function store(){

     $validator = Validator::make(request()->all(),
         [
            'email' => 'required|email|unique:serviceprovider,email|unique:customer,email',
            'full_name' => 'required',
            'username' => 'required|unique:serviceprovider,username|unique:customer,username',
            'mobile' => 'required|unique:serviceprovider,mobile|unique:customer,mobile',
            'password' => 'required',
            'category_id' => 'required',
         ],
         validationMessages()
         );
         if($validator->fails())
         return apiresponse(false,200,$validator->errors()->first());


     $user =  $this->create([
            'email' => request()->email,
            'full_name' => request()->full_name,
            'username' => request()->username,
            'mobile' => request()->mobile,
            'password' => Hash::make(request()->password),
            'category_id'=>request()->category_id,
            'user_key'=>"sp".$this->id
        ]);
        return apiresponse(true, 200, __('auth.verification_code'));


    }


    public function Location(){
        return $this->hasOne(location::class , 'user_key' , 'user_key');
    }

   public function emailcode(){
    return $this->hasMany(emailCode::class ,'user_key' , 'user_key');
   }
   public function mobilecode(){
    return $this->hasMany(mobileCode::class , 'user_key' , 'user_key');
   }

  public function references(){
    return $this->hasMany(reference::class , 'instructor_id');
  }

  public function academicpermission(){
    return $this->hasMany(academicpermission::class , 'user_id');
  }

  public function printer_instructors(){
    return $this->belongsToMany(serviceProvider::class , "printerinstructor" , 'printer_id',  "instructor_id");
}

public function offers(){
    return $this->hasMany(offer::class , 'user_id');
}

public function portfolio(){
    return $this->hasMany(portfolio::class , 'user_id');
}
public function experiences(){
    return $this->hasMany(experiences::class , 'user_id');
}


public function getAvatarAttribute($value)
{
    return env('PROFILE_IMG_URL').$value;
}
}
