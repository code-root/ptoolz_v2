<?php

namespace App\Http\Controllers\registration;

use App\Http\Controllers\Controller;
use App\Models\data\accessorycategory;
use App\Models\data\category;
use Illuminate\Http\Request;

class dataController extends Controller
{

  public function catgeory(){
    return apiresponse(true,200,'success',category::all());
  }




}
