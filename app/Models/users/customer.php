<?php

namespace App\Models\users;

use App\Models\order\eductional\academicpermission;
use App\Models\order\order;
use App\Models\transactions\chargetransaction;
use App\Models\transactions\clientaccounttransaction;
use App\Models\transactions\withdrawaltransaction;
use Laravel\Sanctum\HasApiTokens;
use App\Models\users\data\location;
use App\Models\users\data\emailCode;
use Illuminate\Support\Facades\Hash;
use App\Models\users\data\mobileCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class customer  extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;
    public $table = 'customer';

    public $fillable =
    [
        "id",
        "username",
        "email",
        "mobile",
        "password",
        "full_name",
        "user_key",
        "activity",
        "student",
        "account_type_id",
        "created_at"
    ];

    protected $hidden = ['password'];

    public static function boot() {

	    parent::boot();
	    static::created(function($item) {
            $user_key = 'customer'.$item->id;
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
            'user_key'=>"sp".$this->id
        ]);
      return apiresponse(true, 200, __('auth.verification_code'));

            }


    public function Location(){
        return $this->hasOne(location::class , 'user_key' , 'user_key');
    }

   public function emailcode(){
    return $this->hasMany(emailCode::class , 'user_key' , 'user_key');
   }
   public function mobilecode(){
    return $this->hasMany(mobileCode::class , 'user_key' , 'user_key');
   }


   public function orders(){
    return $this->hasMany(order::class , 'client_id')->orderBy('id','DESC');
   }



public function chargeTransactions(){
    return $this->hasMany(chargetransaction::class , 'user_id')->where('account_type_id' , 1);
 }

 public function withdrawaltransaction(){
    return $this->hasMany(withdrawaltransaction::class , 'user_id')->where('accountTypeId' , 1);
 }

 public function clientaccounttransaction(){
    return $this->hasMany(clientaccounttransaction::class , 'user_id');
 }

 public function holdtransaction(){
    return $this->hasMany(holdtransaction::class , 'user_id');
 }

public function currentBalance(){

 // charge transactions
 $charge  =  $this->chargeTransactions->sum('value');

 // withdral transactions
 $withdral = $this->withdrawaltransaction->sum('value');

 // income transactions
 $incomeAccountTransactions  =  $this->clientaccounttransaction->where('transactionTypeId',4)->sum('value') +  $this->clientaccounttransaction->where('transactionTypeId',3)->sum('value');

// outCome transactions
 $outComeAccountTransactions  =  $this->clientaccounttransaction->where('transactionTypeId',1)->sum('value') +  $this->clientaccounttransaction->where('transactionTypeId',2)->sum('value');

 // total balance
 $balance = ($charge + $incomeAccountTransactions ) - ($withdral + $outComeAccountTransactions);
 return $balance ;
}

public function getAvatarAttribute($value)
{
    return env('PROFILE_IMG_URL').$value;
}

}
