@extends('admin.layout.master')

@section('title', 'Pickup Report')

@section('content')
    <h1 class="mb-1">
        Pickup Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
              <!-- @include('admin.inc.messages')
              <div class="container">
               <div id="search_form" class="row mb-2 justify-content">   
                 <div class="col-8">

                 <div  class="row">   
                   <div class="col-2">
                        <fieldset class="form-group">
                            <select name="search_assigned_by" id="search_assigned_by" class="form-control select2">
                               
                                    <option value="">Select Me</option>
                               
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-2">
                        <fieldset class="form-group">
                            <select name="search_assigned_by" id="search_assigned_by" class="form-control select2">
                                
                                    <option value="">Select Me</option>
                                
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-2">
                        <fieldset class="form-group">
                            <select name="search_assigned_by" id="search_assigned_by" class="form-control select2">
                                
                                    <option value="">Select Me</option>
                                
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-2">
                        <fieldset class="form-group">
                            <select name="search_assigned_by" id="search_assigned_by" class="form-control select2">
                                
                                    <option value="">Select Me</option>
                                
                            </select>
                        </fieldset>
                    </div>

                    </div> -->

                    <!-- <div class="col-4">  -->
                    <!-- <div class="col-4"> -->
                    <!-- <fieldset>
                    <legend>Personalia:</legend>
                    <label for="fname">First name:</label>
                    <input type="text" id="fname" name="fname"><br><br>
                    <label for="lname">Last name:</label>
                    <input type="text" id="lname" name="lname"><br><br>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email"><br><br>
                    <label for="birthday">Birthday:</label>
                    <input type="date" id="birthday" name="birthday"><br><br>
                    <input type="submit" value="Submit">
                     </fieldset> -->
                    <!-- </div> -->
                    <!-- </div>
               <div> -->

            </div>
        </div>
    </div>       

    

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <style>
        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
        .show_active{
            -webkit-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -moz-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
    </style>

@endsection