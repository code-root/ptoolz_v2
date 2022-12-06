<?php

namespace App\Http\Controllers\sp;

use App\Http\Controllers\Controller;
use App\Models\offer\offer;
use App\Models\order\update;
use App\Models\users\serviceProvider;
use Illuminate\Http\Request;

class updatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $offer = offer::find(request()->offer_id);
        $user =serviceProvider::find(Auth('sanctum')->user()->id);
        if ($user->cannot('sendUpdate', $offer)) {
            return apiresponse(false, 401, __('auth.unAuthorized'));
        }

        return apiresponse(true , 200 , 'success' , $offer->updates);


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

        $user =serviceProvider::find(Auth('sanctum')->user()->id);
        $offer = offer::find(request()->offer_id);

        if ($user->cannot('sendUpdate', $offer)) {
            return apiresponse(false, 401, __('auth.unAuthorized'));
        }

       $update = new update();
      return $update->store();
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
        //
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
        //
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
