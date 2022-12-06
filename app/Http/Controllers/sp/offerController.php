<?php

namespace App\Http\Controllers\sp;

use App\Models\offer\offer;
use App\Models\order\order;
use Illuminate\Http\Request;
use App\Models\data\category;
use App\Models\data\ordertype;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\users\serviceProvider;

class offerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = serviceProvider::find(Auth('sanctum')->user()->id);
        $orders = order::where('category_id',$user->category_id)->where('status' , 1)->orderBy('id','DESC')->paginate(10);
        if(request()->order_type_id )
        $orders = order::where('category_id',$user->category_id)->where('order_type_id', request()->order_type_id)->where('status' , 1)->orderBy('id','DESC')->paginate(10);
        else
        $orders = order::where('category_id',$user->category_id)->where('status' , 1)->orderBy('id','DESC')->paginate(10);

        return apiresponse(true, 200 , 'success' , $orders);
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
        // $user = serviceProvider::find(Auth('sanctum')->user()->id);
        // $order = order::find(request()->order_id)

        // if ($user->cannot('acceptAndHold', $offer)) {
        //     return apiresponse(false, 401, __('auth.unAuthorized'));
        // }

        $offer = new offer();
        return $offer->store();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\offer\offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function show(offer $offer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\offer\offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function edit(offer $offer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\offer\offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, offer $offer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\offer\offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function destroy(offer $offer)
    {
        //
    }

  public function order_details(order $order){

    $user = serviceProvider::find(Auth('sanctum')->user()->id);
    if($user->category_id == $order->category_id){
        $model = orderModel($order->order_type_id);
        $orderModel =   $model::find($order->order_id);
        $data = $orderModel->get($order);
        return apiresponse(true , 200 , 'success' , $data);
    }
    else{
        return apiresponse(false , 401 , __('auth.unAuthorized'));
    }

  }


  public function my_offers(){
    $user = serviceProvider::find(Auth('sanctum')->user()->id);

    if(isset(request()->order_type_id)){
        $offers =  $user->offers()->with(['order'=>function($query){
            $query->where('status' ,  request()->status)->where('order_type_id' ,  request()->order_type_id);
           }])->get();
    }else{
        $offers =  $user->offers()->with(['order'=>function($query){
            $query->where('status' ,  request()->status);
           }])->get();
    }

   $data = [];

   foreach($offers as $offer)
   {

    if($offer->order)
    $data[] = $offer->order;
   }

   return apiresponse(true , 200 ,'success' , $data);
  }

}
