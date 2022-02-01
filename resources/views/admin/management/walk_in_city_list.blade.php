@extends('admin.layout.master')

@section('title', 'Walk-In City List')

@section('content')
    <h1>Walk-In City List</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="row align-content-around">
                                <div class="col">
                                    <table class="table table-bordered">
                                            <thead>
                                            <tr role="row" class="bg-primary white text-center">
                                                <th colspan="2" class="border-primary border-darken-1">Rush</th>
                                            </tr>
                                            <tr role="row" class="bg-primary bg-lighten-1 white">
                                                <th class="text-center border-primary border-lighten-2">ID</th>
                                                <th class="border-primary border-lighten-2">Name</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($cities as $city)
                                                @foreach($walk_in_cities as $walk_in_city)
                                                    @if($city->id == $walk_in_city->city_id && $walk_in_city->delivery == 1)
                                                <tr role="row">
                                                    <td class="text-center">{{ $city->id }}</td>
                                                    <td>{{ $city->name }}</td>
                                                </tr>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            </tbody>
                                        </table>
                                </div>
                                <div class="col">
                                    <table class="table table-bordered">
                                            <thead>
                                            <tr role="row" class="bg-primary white text-center">
                                                <th colspan="2" class="border-primary border-darken-1">SaverPlus</th>
                                            </tr>
                                            <tr role="row" class="bg-primary bg-lighten-1 white">
                                                <th class="text-center border-primary border-lighten-2">ID</th>
                                                <th class="border-primary border-lighten-2">Name</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($cities as $city)
                                                @foreach($walk_in_cities as $walk_in_city)
                                                    @if($city->id == $walk_in_city->city_id && $walk_in_city->delivery == 2)
                                                <tr role="row">
                                                    <td class="text-center">{{ $city->id }}</td>
                                                    <td>{{ $city->name }}</td>
                                                </tr>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            </tbody>
                                        </table>
                                </div>
                                <div class="col">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr role="row" class="bg-primary white text-center">
                                            <th colspan="2" class="border-primary border-darken-1">Swift</th>
                                        </tr>
                                        <tr role="row" class="bg-primary bg-lighten-1 white">
                                            <th class="text-center border-primary border-lighten-2">ID</th>
                                            <th class="border-primary border-lighten-2">Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($cities as $city)
                                            @foreach($walk_in_cities as $walk_in_city)
                                                @if($city->id == $walk_in_city->city_id && $walk_in_city->delivery == 3)
                                            <tr role="row">
                                                <td class="text-center">{{ $city->id }}</td>
                                                <td>{{ $city->name }}</td>
                                            </tr>
                                                @endif
                                            @endforeach
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-5">
                                    <form id="city_min_chargeable_info"
                                          class="form-inline" novalidate="novalidate">
                                        <div class="card-bordered">
                                            <div class="card-header text-center white mb-2" style="background-color: #649bc8">
                                                <b>Search Minimum Charges</b>
                                            </div>

                                            <div class="row justify-content-center mb-2 p-1">
                                                <div class="form-group col-6 mb-2">
                                                    <select name="shipping_mode" id="shipping_mode" data-rule-required="true" data-msg-required="Shipping Mode is required" class="select2 form-control required">
                                                        @foreach($shipping_modes as $shipping_mode)
                                                            <option value="{{$shipping_mode->id}}">{{$shipping_mode->mode}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group col-6 mb-2">
                                                    <select name="delivery_type" id="delivery_type" data-rule-required="true" data-msg-required="Delivery Type is required" class="select2 form-control required">
                                                        @foreach($delivery_types as $delivery_type)
                                                            <option value="{{$delivery_type->id}}">{{$delivery_type->delivery_type}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group col-6 mb-2">
                                                    <select name="pickup_city" id="pickup_city" data-rule-required="true" data-msg-required="Pickup City is required" class="select2 form-control required">
                                                        @foreach($pickup_cities as $pickup_city)
                                                            <option value="{{$pickup_city->id}}">{{$pickup_city->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group col-6 mb-2">
                                                    <select name="consignee_city" id="consignee_city" data-rule-required="true" data-msg-required="Consignee City is required" class="select2 form-control required">
                                                    </select>
                                                </div>
                                                <div class="form-group justify-content-center col-12 mb-2">
                                                    <button type="submit" name="search" class="btn btn-primary" value="Search">Search</button>
                                                </div>

                                                <div class="form-group justify-content-center col-12">
                                                    <input type="text" name="min_charges" class="form-control min_charges" id="min_charges" placeholder="Minimum Charges" readonly>
                                                </div>
                                            </div>

                                        </div>
                                        {{--<div class="form-group">--}}
                                        {{--<input type="text" name="pickup"--}}
                                        {{--class="form-control tracking_number" id="tracking_number"--}}
                                        {{--placeholder="Tracking Number">--}}
                                        {{--</div>--}}
                                    </form>
                                </div>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">

        $(document).ready(function () {
            $('#shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Shipping Mode',
            });
            $('#delivery_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Delivery Type',
            });
            $('#pickup_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Pickup City',
            });
            $('#consignee_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Consignee City',
            });

            $('#shipping_mode').on('change', function () {
                $('#consignee_city').empty();
                $('#consignee_city').prepend('<option value="" selected="selected"></option>').select2({
                    width: '100%',
                    placeholder: 'Select Consignee City',
                });
                $('#min_charges').val('');
                var shipping_mode_id = $('#shipping_mode').val();
                var cities = @json($cities);
                var walk_in_cities = @json($walk_in_cities);
                if(shipping_mode_id == 1) {
                    for (var i = 0; i < cities.length; i++) {
                        for (var k = 0; k < walk_in_cities.length; k++) {
                            // console.log(walk_in_cities[k].city_id);
                            if(cities[i].id == walk_in_cities[k].city_id && walk_in_cities[k].delivery == 1) {
                                $('#consignee_city').append('<option value="' + cities[i].id + '">' + cities[i].name + '</option>');
                            }
                        }
                    }
                }
                else if(shipping_mode_id == 2) {
                    for (var i = 0; i < cities.length; i++) {
                        for (var k = 0; k < walk_in_cities.length; k++) {
                            // console.log(walk_in_cities[k].city_id);
                            if(cities[i].id == walk_in_cities[k].city_id && walk_in_cities[k].delivery == 2) {
                                $('#consignee_city').append('<option value="' + cities[i].id + '">' + cities[i].name + '</option>');
                            }
                        }
                    }
                }
                else{
                    for (var i = 0; i < cities.length; i++) {
                        for (var k = 0; k < walk_in_cities.length; k++) {
                            // console.log(walk_in_cities[k].city_id);
                            if(cities[i].id == walk_in_cities[k].city_id && walk_in_cities[k].delivery == 3) {
                                $('#consignee_city').append('<option value="' + cities[i].id + '">' + cities[i].name + '</option>');
                            }
                        }
                    }
                }
            });

            $('#consignee_city, #delivery_type, #pickup_city').on('change', function () {
                $('#min_charges').val('');
            });

            $( "#city_min_chargeable_info" ).validate({
                errorClass:"danger",
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function() {
                    $.ajax({
                        url:'{!! route('admin.management.min_charges') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'pickup_city': $('#pickup_city').val(),
                            'consignee_city': $('#consignee_city').val(),
                            'delivery_type': $('#delivery_type').val(),
                            'shipping_mode': $('#shipping_mode').val()
                        }
                    }).done(function (data) {
                        if(data.status === 1){
                            $('#min_charges').val(data.min_charges);
                        }
                    });

                }
            });
        });

    </script>
@endsection