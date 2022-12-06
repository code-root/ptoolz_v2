<?php

namespace App\Http\Controllers;

use App\Models\data\city;
use App\Models\data\region;
use App\Models\data\country;
use Illuminate\Http\Request;
use App\Models\data\semesters;
use App\Models\users\customer;
use App\Models\data\departments;
use App\Models\data\collegeyears;
use App\Models\users\serviceProvider;
use App\Models\data\systemConfiguration;
use App\Models\order\eductional\college;
use App\Models\order\eductional\reference;
use App\Models\order\eductional\university;

class gloabalDetailsController extends Controller
{
    public function country()
    {
        return apiresponse(true, 200, 'success', country::all());
    }
    public function city(country $country)
    {

        return apiresponse(true, 200, 'success', $country->cities);
    }
    public function region(city $city)
    {
        return apiresponse(true, 200, 'success', $city->regions);
    }


    public function country_universities(country $country)
    {
        $universities =  university::where('country_id', $country->id)->get();

        return apiresponse(true, 200, 'success', $universities);
    }

    public function departments(college $college)
    {
        $departments = $college->departments;
        return apiresponse(true, 200, 'success', $departments);
    }
    public function semesters()
    {
        return apiresponse(true, 200, 'success', semesters::all());
    }

    public function colleges(university $university)
    {
        return apiresponse(true, 200, 'success', $university->colleges);
    }
    public function collegeyears()
    {
        return apiresponse(true, 200, 'success', collegeyears::all());
    }

    public function System_fees()
    {

        $config =  systemConfiguration::latest('id')->first()->adminAcceptPercentage;

        return apiresponse(true, 200, 'success', $config);
    }


    public function instructors_filter()
    {
        $referecnes = reference::where('university_id', request()->university_id)->where('college_id', request()->college_id)->where('department_id', request()->department_id)->where('year_id', request()->year_id)->where('semester_id', request()->semester_id)->with('instructor')->get()->unique('instructor');

        $instructors = [];
        foreach ($referecnes as $referecne)
            $instructors[] = $referecne['instructor'];

        return apiresponse(true, 200, 'success', $instructors);
    }



    public function references_filter()
    {
        $referecnes = reference::where('university_id', request()->university_id)->where('college_id', request()->college_id)->where('department_id', request()->department_id)->where('year_id', request()->year_id)->where('semester_id', request()->semester_id)->where('instructor_id', request()->instructor_id)->get();
        return apiresponse(true, 200, 'success', $referecnes);
    }

    public function user_profie()
    {

        $user = serviceProvider::where('user_key', request()->user_key)->first();

        if ($user != null) {
            $profile =   $user->with(['portfolio', 'experiences' , 'location'])->first();
            $profile->location['country'] = country::find($user->location->country);
            $profile->location['city'] = city::find($user->location->city);
            $profile->location['region'] = region::find($user->location->region);

            return apiresponse(true, 200, 'success', $profile);
        } else {
            $profile = customer::where('user_key', request()->user_key)->with('location')->first();
            $profile->location['country'] = country::find($profile->location->country);
            $profile->location['city'] = city::find($profile->location->city);
            $profile->location['region'] = region::find($profile->location->region);
            return apiresponse(true, 200, 'success', $profile);
        }
    }
}
