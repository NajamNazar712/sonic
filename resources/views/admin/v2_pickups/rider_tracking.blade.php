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
                                                <h3 class="" id="total_riders">{{$riders->count()}}</h3>
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
                                                <h3 class="" id="total_active_riders">{{$riders->where('status',1)->count()}}</h3>
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
                                                <h3 class="" id="total_inactive_riders">{{$riders->where('status',0)->count()}}</h3>
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
                                <select name="search_city" id="search_city" class="form-control select2"
                                        data-rule-required="true" data-msg-required="City is required">
                                    @foreach($cities as $city)
                                        <option value="{{$city->id}}" {{(Auth::user()->default_hub_id != null && Auth::user()->default_hub_id == $city->id) ? 'selected' : (Auth::user()->default_hub_id == null && $city->id == 202 ? 'selected' : '')}}>{{$city->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
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

                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div id="googleMap" style="width:100%;height:400px;"></div>
                        </div>
                    </div>


            </div>
        </div>
    </div>

@endsection

@section('css')

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
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

    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAIg5c-H5DaYBwF_D0HuWliQZQ6XzKj8Nk&sensor=false&libraries=geometry,places,drawing"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

    <script>
        let interval;  // variable to hold setinterval instance

        let picked_icon = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'; // Icon for picked shipment marker
        let not_picked_icon = 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'; // Icon for not-picked shipment marker
        let not_reached_icon = 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png'; // Icon for not-reached shipment marker
        let start_icon = "http://maps.google.com/mapfiles/ms/icons/purple-dot.png"; // Icon for trax marker
        let rider_icon = "http://maps.google.com/mapfiles/ms/icons/pink-dot.png"; // Icon for rider marker

        let markers = [];  // variable for holding all current markers
        let latlngs = []; // array for holding all markers locations
        let map; // variable for holding map reference
        let start_location = new google.maps.LatLng(24.8576669,67.1246698); // trax location

        let start_marker = new google.maps.Marker({
            id: -2,
            position: start_location,
            label: 'Trax',
            icon: start_icon,
            animation: google.maps.Animation.DROP
        }); // Trax marker
        let rider_marker = new google.maps.Marker({
            id: -1,
            icon: rider_icon,
            animation: google.maps.Animation.DROP
        }); // Rider marker

        let rider_location; // Variable for Rider Location
        let rider_status = false; // Check if rider location is present

        var bounds = new google.maps.LatLngBounds();  // google map bound reference
        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer({
            suppressMarkers: true
        }); // suppressMarkers set true to remove default google map markers from screen

        let center = new google.maps.LatLng(24.865720, 67.077394); //Starting Center

        // Creating unique id for marker
        var currentId = 0;
        var uniqueId = function () {
            return currentId++;
        }
    $(document).ready(function (){
        // Initializing Rider Selcet Box
        $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select Rider',
            width: '100%',
            allowClear: true,
        });

        //Initializing City Select Box
        $('#search_city').prepend('<option value=""></option>').select2({
            placeholder: 'Select City',
            width: '100%',
            allowClear: true,
        });

        $('#search_city').on('change',function () {
            let city_id = $(this).val();
            if(city_id == '')
            {
                clearMarkerFromMap();
                ClearRouteFromMap();
                clearInterval(interval);
                SetMapCenter(center);
                return;
            }
            $.ajax({
                url: '{{route("admin.v2_pickups.rider_tracking.by_city")}}',
                method: 'get',
                data:{
                    'city_id': city_id,
                },
                beforeSend: function(){
                    clearMarkerFromMap();
                    ClearRouteFromMap();
                    clearInterval(interval);
                },
                success: function (response) {
                    if(response != 0)
                    {
                        if(response['data'].length > 0)
                        {
                            $.each(response['data'],function (i,v) {
                                latlng = new google.maps.LatLng(v['latitude'], v['longitude']);
                                makeMarker(latlng,v['name'],rider_icon);
                            });

                            setMarkerOnMap(false);
                            SetMapBound(false);
                        }
                        else{
                            // If city latitude longitude are available set map center to city else set it to default center
                            if(response['city_latitude'] != null && response['city_longitude'] != null) {
                                SetMapCenter(new google.maps.LatLng(response['city_latitude'], response['city_longitude']));
                            }
                            else{
                                SetMapCenter(center);
                            }
                        }
                        $('#total_riders').html(response['total_riders']);
                        $('#total_active_riders').html(response['total_active_riders']);
                        $('#total_inactive_riders').html(response['total_inactive_riders']);
                    }
                    else{
                        var error = "Invalid City";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                }
            })
        });

        $('#search_rider').on('change',function () {
            let rider_id = $(this).val();
            if(rider_id == '')
            {
                clearMarkerFromMap();
                ClearRouteFromMap();
                clearInterval(interval);
                SetMapCenter(center);
                return;
            }
            $.ajax({
                url: '{{route("admin.v2_pickups.rider_tracking.by_rider")}}',
                method: 'get',
                data:{
                    'rider_id': rider_id,
                },
                beforeSend: function(){
                    clearMarkerFromMap();
                    ClearRouteFromMap();
                    clearInterval(interval);
                },
                success: function (response) {

                    // Check if we have rider location and place rider marker
                    if(response['rider_latitude'] != null && response['rider_longitude'] != null)
                    {
                        rider_location = new google.maps.LatLng(response['rider_latitude'], response['rider_longitude']);
                        rider_marker.setPosition(rider_location);
                        rider_marker.setLabel(response['rider_location_label']);
                        rider_status = true;
                    }
                    else{
                        rider_status = false;
                    }

                    // check if we have rider pickup note locations
                    if(response['data_length'] > 0) {
                        $.each(response['data'],function (i,v) {
                                latlng = new google.maps.LatLng(v[0]['latitude'], v[0]['longitude']);
                                // Checking Status for Marker Icons
                                if( v[0]['status'] == 'picked') {
                                    var _icon = picked_icon;
                                }
                                if( v[0]['status'] == 'not-picked') {
                                    var _icon = not_picked_icon;
                                }
                                if( v[0]['status'] == 'not-reached') {
                                    var _icon = not_reached_icon;
                                }
                                makeMarker(latlng, v[0]['name'],_icon);
                        });
                    }


                    setMarkerOnMap();

                    // If we have rider pickup locations make route else if we have rider location center map to rider else do nothinhg
                    if(latlngs.length > 0)
                    {
                        ShowRoute(start_location,latlngs[latlngs.length-1],latlngs.slice(0,latlngs.length-1));

                    }
                    else if(rider_status)
                    {
                        SetMapCenter(rider_location);
                    }

                    // Ping rider location
                    interval = setInterval(CheckForRiderLocation,4 * 60 * 1000,rider_id);
                }
            })
        });

        // function to ping rider location
        function CheckForRiderLocation(rider_id)
        {
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
                    if(response['rider_latitude'] != null && response['rider_longitude'] != null)
                    {
                        rider_location = new google.maps.LatLng(response['rider_latitude'], response['rider_longitude']);
                        bounds.extend(rider_location);
                        rider_marker.setPosition(rider_location);
                        rider_marker.setLabel(response['rider_location_label']);
                        rider_status = true;
                    }
                    else{
                        rider_status = false;
                    }

                    if(response['data_length'] > 0) {
                        $.each(response['data'],function (i,v) {
                            latlng = new google.maps.LatLng(v[0]['latitude'], v[0]['longitude']);
                            // Checking Status for Marker Icons
                            if( v[0]['status'] == 'picked') {
                                var _icon = picked_icon;
                            }
                            if( v[0]['status'] == 'not-picked') {
                                var _icon = not_picked_icon;
                            }
                            if( v[0]['status'] == 'not-reached') {
                                var _icon = not_reached_icon;
                            }
                            makeMarker(latlng, v[0]['name'],_icon);
                        });
                        setMarkerOnMap();
                    }

                    SetMapBound();

                }
            })
        }

        // Initializing map
        function initMap() {
            var myMapOptions = {
                zoom: 15,
                center: center,
                mapTypeId: 'roadmap',
                streetViewControl: false,
                fullscreenControl: false,
                mapTypeControl: false,
            };

            map = new google.maps.Map(document.getElementById('googleMap'), myMapOptions);
            // Triggering city select
            $('#search_city').trigger('change');
        }

        // Making marker and setting map bound but not placing marker on map
        function makeMarker(location,label,_icon) {
            var id = uniqueId();
            var marker = new google.maps.Marker({
                id: id,
                position: location,
                label: {
                    text: label,
                    fontWeight: "bold",
                },
                icon: _icon,
                animation: google.maps.Animation.DROP
            });
            bounds.extend(location);
            latlngs[id] = location;
            markers[id] = marker;
        }

        // Removing marker from map and clearing marker and location arrays and bound instance
        function clearMarkerFromMap() {
            bounds = new google.maps.LatLngBounds();
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
            start_marker.setMap(null);
            rider_marker.setMap(null);
            markers = [];
            latlngs = [];
            currentId = 0;
            rider_status = false;
        }

        // clearing route from map
        function ClearRouteFromMap()
        {
            directionsRenderer.setMap(null);
        }

        // setting map center
        function SetMapCenter(_center)
        {
            map.panTo(_center);
            map.setZoom(15);
        }

        // Placing marker on map
        function setMarkerOnMap(status = true) {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(map);
            }
            if(markers.length > 0 && status) {
                start_marker.setMap(map);
            }
            if(rider_status && status) {
                rider_marker.setMap(map);
            }
        }

        // Setting map bound
        function SetMapBound(status = true) {
            if(status) {
                bounds.extend(start_location);
            }
            map.fitBounds(bounds);
        }

        // Showing route from start to end with all points in between
        function ShowRoute(start,end,waypoints_array){
            let waypoints = [];
            $.each(waypoints_array,function(i,v){
                waypoints.push({
                    location: v,
                    stopover: false,
                });
            });
                directionsService.route(
                    {
                        origin: start,
                        destination: end,
                        waypoints: waypoints,
                        optimizeWaypoints: true,
                        travelMode: google.maps.TravelMode.DRIVING,
                    },
                    (response, status) => {
                        if (status === "OK" && response) {
                            directionsRenderer.setDirections(response);
                        } else {
                            SetMapBound();
                            var error = "Directions request failed due to " + status;
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    }
                );

            directionsRenderer.setMap(map);
        }

        initMap();
        });
    </script>
@endsection