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
          <div class="row">
              <div class="col">
                  <div class="card">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-book-open info font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-right">
                                      <h3>278</h3>
                                      <span>Booked Orders</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col">
                  <div class="card">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-book-open info font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-right">
                                      <h3>278</h3>
                                      <span>Booked Orders</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div><div class="col">
                  <div class="card">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-book-open info font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-right">
                                      <h3>278</h3>
                                      <span>Booked Orders</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div><div class="col">
                  <div class="card">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-book-open info font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-right">
                                      <h3>278</h3>
                                      <span>Booked Orders</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

      </div>
    </div>
  </div>
  <!-- ////////////////////////////////////////////////////////////////////////////-->

  @endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/charts/jquery.sparkline.min.js')}}" type="text/javascript"></script>
@endsection