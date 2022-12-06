<?php

namespace App\Http\Controllers\customer;

use App\Models\offer\offer;
use App\Models\order\order;
use App\Models\order\update;
use Illuminate\Http\Request;
use App\Models\users\customer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class updatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {


        $order = order::findorfail(request()->order_id);
        if ($order->status != 2)
            return apiresponse(false, 401, __('auth.unAuthorized'));
        $offer = $order->offers()->where('status', 2)->first();
        $user = customer::find(Auth('sanctum')->user()->id);
        if ($user->cannot('perform_updates', $offer->order)) {
            return apiresponse(false, 401, __('auth.unAuthorized'));
        }
        foreach ($offer->updates as $update) {
            $update->file_name  = $update->file;
            $update->file = env('UPDATES_STRORAGE_LINK') . '/' . $update->file;
        }
        return  apiresponse(true, 200, 'success', $offer->updates);
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
     * @param  \App\Models\order\update  $update
     * @return \Illuminate\Http\Response
     */
    public function show(update $update)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\order\update  $update
     * @return \Illuminate\Http\Response
     */
    public function edit(update $update)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\order\update  $update
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, update $update)
    {
        $offer = $update->offer;
        $user = customer::find(Auth('sanctum')->user()->id);
        if ($user->cannot('perform_updates', $offer->order)) {
            return apiresponse(false, 401, __('auth.unAuthorized'));
        }

        return  $update->decline();
    }


    public function accept_update(update $update)
    {


        $offer = $update->offer;
        $order = $offer->order;


        $transactionController = new orderTransactionsController();

        if ($offer->delivery  ==  1) {

            $update->accept();

            $delivery = $offer->order->delivery;
            $delivery->activity = 1 ;
            $delivery->save();

    }
     elseif ($offer->delivery == 0) {
            //  perform handover
            $update->accept();

            $validator = Validator::make(
                request()->all(),
                [
                    'title' => 'required',
                    'description' => 'required'
                ],
                validationMessages()
            );

            if ($validator->fails())
            return apiresponse(false, 200, $validator->errors()->first());


            $delivery = $offer->order->delivery;

            create_auto_delivery_order($delivery);

            $transactionController->handover_transactions($update->offer);

        }

        return apiresponse(true, 200, 'handoverd successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\order\update  $update
     * @return \Illuminate\Http\Response
     */
    public function destroy(update $update)
    {
        //
    }
}
