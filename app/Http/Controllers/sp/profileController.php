<?php

namespace App\Http\Controllers\sp;

use App\Models\data\city;
use App\Models\data\region;
use App\Models\data\country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\users\data\portfolio;
use App\Models\users\serviceprovider;
use App\Models\users\data\experiences;

class profileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $user = serviceprovider::findOrfail(Auth('sanctum')->user()->id);
       $profile  =  $user->with(['portfolio' ,'experiences' ,'location'])->first();
        $profile->location['country'] = country::find($user->location->country);
        $profile->location['city'] = city::find($user->location->city);
        $profile->location['region'] = region::find($user->location->region);
       return apiresponse(true , 200 , 'success' , $profile);
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
     * @param  \App\Models\users\serviceprovider  $serviceprovider
     * @return \Illuminate\Http\Response
     */
    public function show(serviceprovider $serviceprovider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\users\serviceprovider  $serviceprovider
     * @return \Illuminate\Http\Response
     */
    public function edit(serviceprovider $serviceprovider)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\users\serviceprovider  $serviceprovider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, serviceprovider $serviceprovider)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\users\serviceprovider  $serviceprovider
     * @return \Illuminate\Http\Response
     */
    public function destroy(serviceprovider $serviceprovider)
    {
        //
    }


    public function add_experience(){
        $exp = new experiences();
        return $exp->store();

    }
    public function add_portfolio(){
        $exp = new portfolio();
        return $exp->store();

    }

}
