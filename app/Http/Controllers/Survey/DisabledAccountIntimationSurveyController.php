<?php

namespace App\Http\Controllers\Survey;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DisabledAccountIntimationSurveyController extends Controller
{
    //
    function index()
    {
        dd("its working");
    }

    function survey_details($id)
    {
        dd("its working" , $id);
    }
}
