@extends('admin.layout.master')

@section('title', 'Last Mile Status Report')

@section('content')
    <h1 class="mb-1">
        Last Mile Status Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <div class="col-12 ">
                        <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">

                            <div class="col-4 mt-1">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                    </div>
                                    <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-rule-required="true" data-msg-required="Date(From) is required">
                                </div>
                            </div>
                            <div class="col-4 mt-1">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                    </div>
                                    <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-rule-required="true" data-msg-required="Date(To) is required">
                                </div>
                            </div>
                            <div class="col-4 mt-1">
                                <fieldset class="form-group">
                                    <select name="search_destination" id="search_destination" class="form-control select2">
                                        @foreach($destinations as $destination)
                                            <option value="{{$destination->id}}">{{$destination->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-4 mt-1">
                                <fieldset class="form-group">
                                    <select name="search_hub" id="search_hub" class="form-control select2">
                                        @foreach($hubs as $hub)
                                            <option value="{{$hub->id}}">{{$hub->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-4 mt-1">
                                <fieldset class="form-group">
                                    <select name="search_zone" id="search_zone" class="form-control select2">
                                        @foreach($zones as $zone)
                                            <option value="{{$zone->id}}">{{$zone->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-4 mt-1">
                                <fieldset class="form-group">
                                    <select name="search_rider" id="search_rider" class="form-control select2">
                                        @foreach($riders as $rider)
                                            <option value="{{$rider->id}}">{{$rider->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-2 mt-1">
                                <div class="form-group">
                                    <button type="submit" id="search_filter_btn" class="btn btn-outline-info btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="" id="report_data">

                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">Time Slot</th>
                            <th class="border-primary border-darken-1">Total Out for Delivery</th>
                            <th class="border-primary border-darken-1">Total Statuses Updated</th>
                            <th class="border-primary border-darken-1">Percentage</th>
                            <th class="border-primary border-darken-1">Statuses Updated from Bolt</th>
                            <th class="border-primary border-darken-1">Percentage</th>
                            <th class="border-primary border-darken-1">Statuses Updated from Sonic</th>
                            <th class="border-primary border-darken-1">Percentage</th>
                            <th class="border-primary border-darken-1">Number Of Delivered From Bolt</th>
                            <th class="border-primary border-darken-1">Number Of Undelivered From Bolt</th>
                            <th class="border-primary border-darken-1">Number Of Delivered From Sonic</th>
                            <th class="border-primary border-darken-1">Number Of Undelivered From Sonic</th>

                        </tr>
                        </thead>
                    </table>
                </div>
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
        span.font-13{
            font-size: 13px;
        }
    </style>

@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Hub',
                width:'100%',
                allowClear:true
            });
            $('#search_zone').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Zone',
                width:'100%',
                allowClear:true
            });
            $('#search_destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Destination',
                width:'100%',
                allowClear:true
            });
            $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Rider',
                width:'100%',
                allowClear:true
            });
            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                min: new Date(2020, 0, 1),
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #to_date').pickadate('picker').set('min', $('#search_form #from_date').pickadate('picker').get('select'));
                    }
                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                min: new Date(2020, 0, 1),
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #from_date').pickadate('picker').set('max', $('#search_form #to_date').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {

                    list();
                }
            });


            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Last Mile Status Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                        action: function (e, dt, node, config) {
                            var that = this;
                            $.ajax({
                                url: '{!! route('admin.reports.last_mile_status.list') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'excel': true,
                                }
                            }).done(function (data) {
                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(that,e, dt, node, config);
                            });
                        },
                    },
                ],
                scrollX: true,
                paging: false,
                ordering: false,
            });

                var types = ['time','out_for_delivery_count', 'total_status_updated','out_for_delivery_percentage', 'bolt_status_updated', 'bolt_status_percentage', 'sonic_status_updated', 'sonic_status_percentage','total_shipment_deliverd_bolt','total_shipment_undeliverd_bolt','total_shipment_deliverd_sonic','total_shipment_undeliverd_sonic'];
            function list() {
                blockPagePermanently();
                $('#search_filter_btn').prop('disabled', true);
                $('#export_btn').prop('disabled', true);

                $.ajax({
                    url: '{!! route('admin.reports.last_mile_status.list') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'search_date_from' : $('input[name="from_date_formatted"]').val(),
                        'search_date_to' : $('input[name="to_date_formatted"]').val(),
                        'search_hub' : $('#search_hub').val(),
                        'search_zone' : $('#search_zone').val(),
                        'search_destination' : $('#search_destination').val(),
                        'search_rider' : $('#search_rider').val()
                    }
                })
                    .done(function(data) {
                            if(data.status == 0){
                                table.clear();

                                $.each(data.time_slots, function(index, count) {
                                    var row = [];

                                    $.each(types, function(index, type) {
                                        row.push(count[type]);
                                    });

                                    table.row.add(row);

                                });

                                table.draw();
                            }



                        $('#search_filter_btn').prop('disabled', false);

                        UnblockPagePermanently();
                    });
            }
        });

    </script>
@endsection