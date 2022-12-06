<?php

namespace App\Http\Controllers\sp;

use App\Http\Controllers\Controller;
use App\Models\order\eductional\academicpermission;
use App\Models\order\eductional\reference;
use App\Models\order\eductional\university;
use App\Models\users\serviceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class referenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = serviceProvider::find((Auth('sanctum')->user()->id));
        if ($user->category_id == 1) {
            $universities = university::all();
            $university_references =[];
            $data = [];
            $instructors = request()->instructor_id ==null ?   $user->printer_instructors : collect([serviceProvider::find(request()->instructor_id)]);
            foreach($universities as $university){
            $out_university = new university();
                foreach($university->references as $reference){
                    if($instructors->contains($reference->instructor)){
                   array_push($university_references , $reference);
                    }
               }
               $out_university->name =$university->name;
               $out_university->id =$university->id;
               $out_university->references =collect($university_references);
               array_push($data , $out_university);
            //    var_dump($out_university);

            }

      return apiresponse(true , 200 , 'success' , $data);

        }
         elseif ($user->category_id == 10) {

            $global = (isset(request()->from_date) && isset(request()->to_date));
            $universities = university::all();
            $university_references =[];
            $data = [];
            foreach ($universities as $university) {
                $out_university = new university();
                $max = $global ? request()->to_date :  $university->references->max('created_at')  ;
                $min = $global ? request()->from_date :  $university->references->min('created_at')  ;
                $university->printer_references = $university->instructors_references()->where("created_at" , '>=' ,$min)->where("created_at" , '<=' ,$max)->get();
                $out_university->name =$university->name;
                $out_university->id =$university->id;
                $out_university->references =collect($university->printer_references);
                array_push($data , $out_university);
            }
            return apiresponse(true , 200 , 'success' , $data);


        }


        // return apiresponse(true, 200, 'success', $universities);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = serviceProvider::find((Auth('sanctum')->user()->id));
        $permession =  academicpermission::where('user_id', $user->id)->where('status', 1)->count();
        if ($user->category_id == 10 || $permession) {
            $reference = new reference();
            return  $reference->store();
        }
        return apiresponse(false, 401, __('auth.unAuthorized'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\order\eductional\reference  $reference
     * @return \Illuminate\Http\Response
     */
    public function show(reference $reference)
    {


      $user = serviceProvider::find((Auth('sanctum')->user()->id));

        if($user->category_id == 1){
            if($user->printer_instructors->contains($reference->instructor))
            {
                $count = $reference->downloads()->count();
                $clients = $reference->downloads;
                return apiresponse(true,200 , 'success' ,['count'=>$count , 'clients'=>$clients] );

            }
        }
        else{
            if($user->id == $reference->instructor->id)
            {   $count = $reference->downloads()->count();
                $clients = $reference->downloads;
                return apiresponse(true,200 , 'success' ,['count'=>$count , 'clients'=>$clients] );

            }
        }
        return  apiresponse(true,401,__('auth.unAuthorized'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\order\eductional\reference  $reference
     * @return \Illuminate\Http\Response
     */
    public function edit(reference $reference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\order\eductional\reference  $reference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, reference $reference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\order\eductional\reference  $reference
     * @return \Illuminate\Http\Response
     */
    public function destroy(reference $reference)
    {
        $user = serviceProvider::find((Auth('sanctum')->user()->id));

        if($user->category_id == 1){
            if($user->printer_instructors->contains($reference->instructor))
            {
                $reference->delete();
                return  apiresponse(true,200,'deleted successfully');
            }
        }
        else{
            if($user->id == $reference->instructor->id)
            {
                $reference->delete();
                return  apiresponse(true,200,'deleted successfully');
            }
        }
        return  apiresponse(true,401,__('auth.unAuthorized'));

    }

    public function academic_permission()
    {
        $user = serviceProvider::find((Auth('sanctum')->user()->id));
        if ($user->category_id == 1) {
            $user->academicpermission()->create();
            return apiresponse(true, 200, 'request sent');
        }
        return apiresponse(false, 401, __('auth.unAuthorized'));
    }


    public function printer_instructors()
    {
        $user = serviceProvider::find((Auth('sanctum')->user()->id));

        if ($user->category_id == 1) {
            return  apiresponse(true,200,'success',$user->printer_instructors);
        }

        return apiresponse(false, 401, __('auth.unAuthorized'));
    }



}
