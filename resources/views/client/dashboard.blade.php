@extends('client.layout.master')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
      <div class="content-header row">
      </div>
      <div class="content-body">
        
        
       
        <!-- Active Orders -->
       <h1>Welcome To Trax Logistics,
       <span class="user-name text-bold-700 ">{{Auth::user()->name}}</span>
     </h1>
        <!-- Active Orders -->
      </div>
    </div>
  </div>
  <!-- ////////////////////////////////////////////////////////////////////////////-->

  @endsection