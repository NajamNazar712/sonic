@extends('admin.layout.master')

@section('title', 'Rider Tracking')

@section('content')
    <h1 class="mb-1">
        Rider Tracking
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

{{--                <form action="{{route('admin.management.route.add')}}" method="post" class="mt-2" id="addRouteForm"--}}
{{--                      novalidate="novalidate">--}}
{{--                    @csrf--}}

                    <div class="row mb-2">
                        <div class="col-4">
                            <div class="card bg-gradient-directional-total pull-up">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-user text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-right">
                                                <h3 class="">{{$riders->count()}}</h3>
                                                <span>Total Rider(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card bg-gradient-directional-active pull-up">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-user-follow text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-right">
                                                <h3 class="">{{$riders->where('status',1)->count()}}</h3>
                                                <span>Active Rider(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card bg-gradient-directional-inactive pull-up">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-user-unfollow text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-right">
                                                <h3 class="">{{$riders->where('status',0)->count()}}</h3>
                                                <span>In-Active Rider(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2 justify-content-center">
                        <div class="col-4">
                            <fieldset class="form-group">
                                <select name="search_rider" id="search_rider" class="form-control select2"
                                        data-rule-required="true" data-msg-required="Rider is required">
                                        @foreach($riders as $rider)
                                            <option value="{{$rider->id}}">{{$rider->name}}</option>
                                        @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-4">
                            <fieldset class="form-group">
                                <select name="search_city" id="search_city" class="form-control select2"
                                        data-rule-required="true" data-msg-required="City is required">
                                    @foreach($cities as $city)
                                        <option value="{{$city->id}}" {{$city->id == 202 ? 'selected' : ''}}>{{$city->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div id="googleMap" style="width:100%;height:400px;"></div>
                        </div>
                    </div>

{{--                </form>--}}

            </div>
        </div>
    </div>

@endsection

@section('css')

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

    <style>
        .bg-gradient-directional-total {
            background-image: linear-gradient(45deg, #027d8a, #01e4e4);
            background-repeat: repeat-x;
        }
        .bg-gradient-directional-active {
            background-image: linear-gradient(45deg, #076500, #11f118);
            background-repeat: repeat-x;
        }
        .bg-gradient-directional-inactive {
            background-image: linear-gradient(45deg, #ff0c0c, #ff9191);
            background-repeat: repeat-x;
        }
    </style>
@endsection

@section('js')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCV6MaF4JjDpjuYljaUw9NxEY5kf5ipOzc&sensor=false&libraries=geometry,places,drawing"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

    <script>
        let markers = [];
        var bounds = new google.maps.LatLngBounds();
        let map;
        let center = new google.maps.LatLng(24.865720, 67.077394);
        var currentId = 0;
        var uniqueId = function () {
            return currentId++;
        }
        $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select Rider',
            width: '100%',
            allowClear: true
        });

        $('#search_city').select2({
           width: '100%',
        });

        $('#search_rider').on('change',function () {
            let rider_id = $(this).val();
            $.ajax({
                url: '{{route("admin.v2_pickups.rider_tracking.by_rider")}}',
                method: 'get',
                data:{
                    'rider_id': rider_id,
                },
                beforeSend: function(){
                    clearMarkerFromMap();
                },
                success: function (response) {

                    $.each(response,function (i,v) {
                        latlng = new google.maps.LatLng(v['latitude'], v['longitude']);
                        makeMarker(latlng,v['name']);
                    });
                    if(response != '') SetMapBound();

                }
            })
        });

        function initMap() {
            // const directionsService = new google.maps.DirectionsService();
            // const directionsRenderer = new google.maps.DirectionsRenderer();
            var myMapOptions = {
                zoom: 15,
                center: center,
                mapTypeId: 'roadmap',
                streetViewControl: false,
                fullscreenControl: false,
                mapTypeControl: false,
            };

            map = new google.maps.Map(document.getElementById('googleMap'), myMapOptions);
        }

        function makeMarker(location,label) {
            var id = uniqueId();
            var marker = new google.maps.Marker({
                id: id,
                position: location,
                label: label,
                // icon: yellow_flag_path,
                map: map,
                animation: google.maps.Animation.DROP
            });
            bounds.extend(location);
            markers[id] = marker;
        }
    
        function clearMarkerFromMap() {
            bounds = new google.maps.LatLngBounds();
            setMarkersOnMap(null);
            map.panTo(center);
            markers = [];
            currentId = 0;
        }

        function setMarkersOnMap(map) {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(map);
            }
        }

        function SetMapBound() {
            map.fitBounds(bounds);
        }


        // directionsRenderer.setMap(map); // Existing map object displays directions
        // // Create route from existing points used for markers
        // const route = {
        //     origin: dakota,
        //     destination: frick,
        //     travelMode: 'DRIVING'
        // }
        //
        // directionsService.route(route,
        //     function(response, status) { // anonymous function to capture directions
        //         if (status !== 'OK') {
        //             window.alert('Directions request failed due to ' + status);
        //             return;
        //         } else {
        //             directionsRenderer.setDirections(response); // Add route to the map
        //             var directionsData = response.routes[0].legs[0]; // Get data about the mapped route
        //             if (!directionsData) {
        //                 window.alert('Directions request failed');
        //                 return;
        //             }
        //             else {
        //                 document.getElementById('msg').innerHTML += " Driving distance is " + directionsData.distance.text + " (" + directionsData.duration.text + ").";
        //             }
        //         }
        //     });
        initMap();
    </script>
@endsection