@extends('admin.layout.master')

@section('title', 'OPS Report')

@section('content')
    <h1 class="mb-1">
        OPS Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="search_form" class="form-inline mb-1 mt-3 justify-content-center" novalidate="novalidate">
                    <div class="col-3 mb-1">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span
                                    class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left date_css">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="from_date"
                                class="form-control bg-primary border-primary white rounded-right" id="from_date"
                                placeholder="Date From" data-rule-required="true" data-msg-required="This field is required">
                        </div>
                    </div>
                    <div class="col-3 mb-1">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span
                                    class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left date_css">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="to_date"
                                class="form-control bg-primary border-primary white rounded-right" id="to_date"
                                placeholder="Date To" data-rule-required="true" data-msg-required="This field is required">
                        </div>
                    </div>

                    <div class="col-3 mb-1">
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-info btn-min-width"><i class="la la-search"></i>
                                Search</button>
                        </div>
                    </div>

                    
                </form>


               
                <div id="table" style="overflow-x: scroll;">
                    <table class="table table-bordered" style="z-index: 3;">
                        <thead>
                            <tr class="bg-primary white">
                                <th class="border-primary border-darken-1"></th>
                                <th class="border-primary border-darken-1" colspan="6">On Route Summary</th>
                                <th class="border-primary border-darken-1" colspan="2">Delivered</th>
                                <th class="border-primary border-darken-1" colspan="5">COD Amount Summary</th>
                                <th class="border-primary border-darken-1" colspan="2">Status Not Updated</th>
                                <th class="border-primary border-darken-1" colspan="4">Undelivered Summary</th>
                            </tr>
                            <tr class="bg-primary white">
                                <th class="border-primary border-darken-1">Region & Zone</th>

                                <th class="border-primary border-darken-1">Ready For Delivery</th>
                                <th class="border-primary border-darken-1">Out For Delivery</th>
                                <th class="border-primary border-darken-1">Out For Delivery %</th>
                                <th class="border-primary border-darken-1">Delivery Note</th>
                                <th class="border-primary border-darken-1">Pending Deliveries</th>
                                <th class="border-primary border-darken-1">Pending Deliveries %</th>

                                <th class="border-primary border-darken-1">Delivered</th>
                                <th class="border-primary border-darken-1">Delivered %</th>

                                <th class="border-primary border-darken-1">Delivered COD Amount</th>
                                <th class="border-primary border-darken-1">COD Submitted Via Konnect</th>
                                <th class="border-primary border-darken-1">COD Submitted Via Fintech</th>
                                <th class="border-primary border-darken-1">COD Submitted Via Cash</th>
                                <th class="border-primary border-darken-1">COD Short Submitted By Rider</th>

                                <th class="border-primary border-darken-1">Pending</th>
                                <th class="border-primary border-darken-1">Pending %</th>

                                <th class="border-primary border-darken-1">Undelivered</th>
                                <th class="border-primary border-darken-1">Undelivered %</th>
                                <th class="border-primary border-darken-1">RCP</th>
                                <th class="border-primary border-darken-1">RCP %</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">

    <style>
        .date_css {
            height: 40px;
            margin: 1px;
        }

        #rating {
            background-color: white;
            border: 1px solid #ccc;
            padding: 5px;
        }

        #rating option img {
            width: 20px;
            height: 20px;
            vertical-align: middle;
            margin-right: 5px;
        }
        
    </style>
@endsection
@section('js')
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/legacy.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pagination/moment.min.js') }}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format: 'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #to_date').pickadate('picker').set('min', $(
                            '#search_form #from_date').pickadate('picker').get('select'));
                    }
                }
            });

            var date_limit = '{{ Carbon\Carbon::now()->toDateString() }}';
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format: 'dd mmmm, yyyy',
                max: new Date(date_limit),
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #from_date').pickadate('picker').set('max', $(
                            '#search_form #to_date').pickadate('picker').get('select'));
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
                    $.ajax({
                        url: '{!! route('admin.reports.ops_report.list') !!}',
                        method: 'GET',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'from_date': $('#search_form input[name="from_date_formatted"]').val(),
                            'to_date': $('#search_form input[name="to_date_formatted"]').val(),
                        },
                        success: function(result) {
                            if(result.status == 200)
                            {
                                
                                $.each(result.data, function(region_index, region) {

                                    var html = '';
                                    html += '<tr> <th> ' + region_index + ' </th> <th colspan="19"></th></tr>';

                                    $.each(region, function(zone_index, z) {
                                        html += '<tr>';
                                            html += '<td> ' + z.zone + ' </td>';
                                            html += '<td> ' + z.ready_for_delivery + ' </td>';
                                            html += '<td> ' + z.out_for_delivery + ' </td>';
                                            html += '<td> ' + z.out_for_delivery_percentage + ' </td>';
                                            html += '<td> ' + z.delivery_note + ' </td>';
                                            html += '<td> ' + z.pending_deliveries + ' </td>';
                                            html += '<td> ' + z.pending_deliveries_percentage + ' </td>';
                                            html += '<td> ' + z.delivered + ' </td>';
                                            html += '<td> ' + z.delivered_percentage + ' </td>';
                                            html += '<td> ' + z.delivered_cod_amount + ' </td>';
                                            html += '<td> ' + z.cod_submitted_via_konnect + ' </td>';
                                            html += '<td> ' + z.cod_submitted_via_fintech + ' </td>';
                                            html += '<td> ' + z.cod_submitted_via_cash + ' </td>';
                                            html += '<td> ' + z.cod_submitted_by_rider + ' </td>';
                                            html += '<td> ' + z.pending + ' </td>';
                                            html += '<td> ' + z.pending_percentage + ' </td>';
                                            html += '<td> ' + z.undelivered + ' </td>';
                                            html += '<td> ' + z.undelivered_percentage + ' </td>';
                                            html += '<td> ' + z.rcp + ' </td>';
                                            html += '<td> ' + z.rcp_percentage + ' </td>';
                                        html += '</tr>';
                                        console.log(region_index,z);
                                    });
                                   

                                    $('#table tbody').append(html);
                                   
                                });
                            }
                            else{
                                console.log("error");
                            }

                            
                        },
                        error: function(error) {
                            console.log('AJAX error: ' + error);
                        }
                    });
                }
            });

        });
    </script>
@endsection
