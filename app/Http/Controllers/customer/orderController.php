<?php

namespace App\Http\Controllers\customer;

use App\Models\data\tax;
use App\Models\data\city;
use App\Models\data\region;
use App\Models\offer\offer;
use App\Models\order\order;
use App\Models\data\country;
use Illuminate\Http\Request;
use App\Models\data\category;
use App\Models\data\ordertype;
use App\Models\users\customer;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\order\orderDelivery;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class orderController extends Controller
{

    function __construct()
    {
        if (getRequestLanguage()  != 'en')
            App::setLocale(getRequestLanguage());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

     $user = customer::find(Auth('sanctum')->user()->id);

     $status = request()->status ;
     if(request()->category_id )
     $orders = $user->orders()->where('status', $status)->where('category_id' , request()->category_id)->withcount('offers')->paginate(10);
     else
     $orders = $user->orders()->where('status', $status)->withcount('offers')->paginate(10);

     return apiresponse(true, 200 , 'success' , $orders);

    }


    public function my_orders(){

        $user = customer::find(Auth('sanctum')->user()->id);

        $status = request()->status ;

        $response = [];

        if(request()->category_id )
        $orders = order::where('client_id', $user->id)->where('status', $status)->where('category_id' , request()->category_id)->withcount('offers')->having('offers_count' , 0);
        else
        $orders =order::where('client_id', $user->id)->where('status', $status)->withcount('offers')->having('offers_count' , 0);


        return apiresponse(true, 200 , 'success' , $orders->paginate(10));


       }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    //    $ext = explode('/' , $_FILES['file']['type'])[1];

    //     $fileContent = file_get_contents($_FILES['file']['tmp_name']);
    //    $path =  Storage::disk('public')->put(time().'.'.$ext, $fileContent);
    $validator = Validator::make(request()->all(),

    [
        "order_type_id"=>'required',
    ]);

    if($validator->fails())
    return apiresponse(false,200,$validator->errors()->first());
       $model = orderModel(request()->order_type_id);
       $order  =new $model();
      return $order->store();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\$s\order\order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(order $order)
    {
        $user = customer::find(Auth('sanctum')->user()->id);
        if($user->orders->contains($order)){
            $model = orderModel($order->order_type_id);
            $orderModel =   $model::find($order->order_id);
            if(isset(request()->update_photography))
            $data = $orderModel->get_update($order);
            else
            $data = $orderModel->get($order);
            return apiresponse(true , 200 , 'success' , $data);
        }
        else{
            return apiresponse(false , 401 , __('auth.unAuthorized'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\order\order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\order\order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, order $order)
    {
        $user  = customer::find(Auth('sanctum')->user()->id);

        if ($user->cannot('update_delete_order', $order)) {
            return apiresponse(false, 401, __('auth.unAuthorized'));
        }


        $model = orderModel($order->order_type_id);
        $orderModel =   $model::find($order->order_id);
       return $orderModel->edit($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\order\order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(order $order)
    {
        $user  = customer::find(Auth('sanctum')->user()->id);

        if ($user->cannot('update_delete_order', $order)) {
            return apiresponse(false, 401, __('auth.unAuthorized'));
        }

        $model = orderModel($order->order_type_id);
        $orderModel =   $model::find($order->order_id);
        //
        $orderModel->delete();
         $order->delete();

       return apiresponse(true , 200 , 'order deleted successfully');

    }


public function offers(order $order){
    $user  = customer::find(Auth('sanctum')->user()->id);

    if ($user->cannot('show_offers', $order)) {
        return apiresponse(false, 401, __('auth.unAuthorized'));
    }

    foreach($order->offers  as $offer ){
        $offer->total_cost =offerCost($offer) ;
        $offer->tax = tax::where('country', $order->user->country)->first()->taxPercentage ?? 10;
       $offer->full_name = $offer->user->full_name;
        $offer->avatar = env("PROFILE_IMG_URL").$offer->user->avatar;
        $offer->user_key = $offer->user->user_key;
        unset($offer['user']);
    }
  return apiresponse(true , 200 ,'success' , $order->offers);


}

    public function invoice(offer $offer){


        $user = customer::find(Auth('sanctum')->user()->id);
        // $tax = 14;


        // return $user;

        if ($user->cannot('show_offers', $offer->order)) {
            return apiresponse(false, 401, __('auth.unAuthorized'));
        }
        $tax =  tax::where('country', $user->Location->country)->first()->taxPercentage ?? 10;
        $data = [];
        $data['order'] = $offer->order;
        // return $offer->order->delivery;
        // return $offer;
        if($offer->delivery == 1){
        $data['location'] = $offer->order->delivery;
        $data['location']->country = country::find($offer->order->delivery->country)->country_name;
        $data['location']->city = city::find($offer->order->delivery->city)->city_name;
        $data['location']->region = region::find($offer->order->delivery->region)->region_name;
        // array_($data, 'location');
        }
        $data['offer']=$offer;
        $data['offer']->tax=$tax;
        $data['offer']->total_cost=offerCost($offer);
        unset($data['location']);
        unset($data['offer']->order);

       return apiresponse(true , 200 , 'success' , $data);


       }





   public function cancel_offer(offer $offer){
     $user = customer::find(Auth('sanctum')->user()->id);
     if($offer->order->user->id == $user->id){
        $offer->delete();
        return apiresponse(false , 200 , 'offer deleted successfully');
     }
     else
     return apiresponse(false , 401 , __('auth.unAuthorized'));


   }
}
