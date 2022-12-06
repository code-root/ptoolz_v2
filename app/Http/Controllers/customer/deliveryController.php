<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\order\order;
use App\Models\order\orderDelivery;
use App\Models\users\data\location;
use Illuminate\Http\Request;

class deliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $order_id = request()->order_id;
        $orderDelivery = orderDelivery::where('order_id', $order_id)->first();
        $order  = order::find($order_id);
       $offer = $order->offers->where('status' , 2)->first();
       $location = location::find($orderDelivery->from_location)->with('country')->with('city')->with('region')->first();
       $orderDelivery->sp_location = $location;

     return apiresponse(true, 200, 'success', $orderDelivery->get());
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\order\orderDelivery  $orderDelivery
     * @return \Illuminate\Http\Response
     */
    public function show(orderDelivery $orderDelivery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\order\orderDelivery  $orderDelivery
     * @return \Illuminate\Http\Response
     */
    public function edit(orderDelivery $orderDelivery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\order\orderDelivery  $orderDelivery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, orderDelivery $orderDelivery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\order\orderDelivery  $orderDelivery
     * @return \Illuminate\Http\Response
     */
    public function destroy(orderDelivery $orderDelivery)
    {
        //
    }

    public function hand_over(orderDelivery $orderDelivery)
    {

        $code = request()->qr_code;
        if ($code == $orderDelivery->qr_code) {
            $offer = $orderDelivery->order()->where('status', 2)->first;
            $transactionController = new orderTransactionsController();
            $transactionController->handover_transactions($offer);
            return apiresponse(true, 200, 'handoverd_successfully');
        }
        return apiresponse(false, 401, __('auth.unAuthorized'));
    }
}
