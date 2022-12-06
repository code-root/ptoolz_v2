<?php

namespace App\Http\Controllers\sp;

use App\Http\Controllers\Controller;
use App\Models\order\orderDelivery;
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
        //
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
    public function confirm_recieve(orderDelivery $orderDelivery){
        $offer = $orderDelivery->order()->where('status', 2)->first;

        $orderDelivery->sp_received = 1;
        $orderDelivery->save();

        return apiresponse(true , 200 , 'success');

    }
}
