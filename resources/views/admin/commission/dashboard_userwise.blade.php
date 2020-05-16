@extends('admin.layout.master')

@section('title', 'Commission Dashboard')

@section('content')
    <h1 class="mb-1">
        Commission Dashboard
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 username">
                <h3>{{$currentuser}} </h3>
                </div>
                <div class="row mb-2 justify-content-center">
                    <div class="col-12 ">
                        <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                            @csrf
                            <div class="col-4">
                                <div class="form-group pb-1">
                                    <select name="shipper" class="select2" id="shipper" data-rule-required="true" data-msg-required="Shipper is required">
                                        @foreach($shippers as $shipper)
                                            <option value="{{ $shipper->id }}">{{ $shipper->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                    </div>
                                    <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-rule-required="true" data-msg-required="Date(From) is required" data-value="{{$first_day}}">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                    </div>
                                    <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-rule-required="true" data-msg-required="Date(To) is required" data-value="{{$last_day}}">
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="form-group">
                                    <!-- <button id = "search_filter_btn" type="button" class="btn btn-outline-info btn-min-width"><i class="la la-search"></i> Search</button> -->
                                    <button type="submit" class="btn btn-outline-info btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="" id="report_data">
                    <div class="row">
                        <input type="hidden" id="cards_filter_input">
                        <div class="col-3">
                            <div class="card pull-up">
                                <div class="card-content border rounded" id="shipments_booked">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-grid font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-right">
                                                <h3 id="booked">{{$stats['booked']}}</h3>
                                                <span>Shipment(s) Booked</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card bg-gradient-directional-primary pull-up">
                                <div class="card-content" id="shipments_received">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-grid text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="received">{{$stats['received']}}</h3>
                                                <span>Shipment(s) Received</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card bg-gradient-directional-info pull-up">
                                <div class="card-content" id="revenue_earned">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-layers text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="revenue">{{$stats['revenue']}}</h3>
                                                <span class="font-13">Revenue Earned</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><div class="col-3">
                            <div class="card bg-gradient-directional-success pull-up">
                                <div class="card-content" id="commission_earned">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-check text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="commission">{{$stats['commission']}}</h3>
                                                <span>Commission Earned</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <table class="table table-bordered datatable" id="datatable" style="width:100%;z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Account ID</th>
                            <th class="border-primary border-darken-1">Shipper Name</th>
                            <th class="border-primary border-darken-1">Shipments Booked</th>
                            <th class="border-primary border-darken-1">Shipments Received</th>
                            <th class="border-primary border-darken-1">Revenue</th>
                            <th class="border-primary border-darken-1">Commission</th>
                            <th class="border-primary border-darken-1">Commission Amount</th>

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
        .username{
            padding-left:20px;
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
            $('#search_form #shipper').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Shipper',
                allowClear:true
            });
            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                select: '{{$first_day}}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
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
                select: '{{$last_day}}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #from_date').pickadate('picker').set('max', $('#search_form #to_date').pickadate('picker').get('select'));
                    }
                }
            });

           

            // jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            //     if ( this.context.length ) {
            //         body = [];
            //         var params = table.ajax.params();
            //         params.start = 0;
            //         params.length = -1;
            //         var jsonResult = $.ajax({
            //             url: '{{ route('admin.reports.summary.list') }}',
            //             data: params,
            //             success: function (result) {
            //                 head = [];

            //                 head.push('S. No.');
            //                 head.push('Tracking No.');
            //                 head.push('Order ID');
            //                 head.push('Shipper');
            //                 head.push('Status');
            //                 head.push('Payment Status');
            //                 head.push('Service Type');
            //                 head.push('Arrival Date');
            //                 head.push('Origin');
            //                 head.push('Destination');
            //                 head.push('Consignee Name');
            //                 head.push('Consignee Contact');
            //                 head.push('Consignee Address');
            //                 head.push('Collection Amount');
            //                 head.push('Booking Date');


            //                 $.each(result.data, function(index, values) {
            //                     row = [];

            //                     row.push(index + 1);
            //                     row.push(values.tracking_number);
            //                     row.push(values.order_id);
            //                     row.push(values.shipper);
            //                     row.push(values.current_status);
            //                     row.push(values.payment_status);
            //                     row.push(values.service_type);
            //                     row.push(values.arrival_date);
            //                     row.push(values.origin);
            //                     row.push(values.destination);
            //                     row.push(values.consignee_name);
            //                     row.push(values.consignee_phone);
            //                     row.push(values.consignee_address);
            //                     row.push(values.collection_amount);
            //                     row.push(values.booking_date);
            //                     body.push(row);
            //                 });
            //             },
            //             async: false
            //         });

            //         return {body: body, header:head};
            //     }
            // } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Summary Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.dashboard.list') }}',
                    data: function (d) {
                        d.search_shipper = $('#shipper').val();
                       // d.cards_filter = $('#cards_filter_input').val();
                       d.search_date_from = $('input[name="from_date_formatted"]').val();
                       d.search_date_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'account_id' ,name: 'u.id', class: 'align-middle text-center account_id'},
                    { data:'shipper' ,name: 'u.name', class: 'align-middle shipper'},
                    { data:'booked' ,name: 'booked', class: 'align-middle booked'},
                    { data:'received' ,name: 'received', class: 'align-middle received'},
                    { data:'revenue' ,name: 'revenue', class: 'align-middle revenue'},
                    { data:'commission' ,name: 's.commission', class: 'align-middle commission'},
                     { data:'commission_amount' ,name: 'commission_amount', class: 'align-middle commission_amount'},
                    
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });
            $('#search_filter_btn').on('click',function () {
                table.draw();
            });
            function add_animation(box) {
                $("#report_data div").removeClass("show_active");
                box.addClass('show_active');
            }

            $('#search_form').validate({
                    errorClass: 'danger',
                    successClass: 'success',
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parents('.form-group'));
                    },
                    submitHandler: function(form) {
                        var from_date = $('#search_form input[name="from_date_formatted"]').val();
                        var to_date = $('#search_form input[name="to_date_formatted"]').val();
                        var shipper = $('#shipper').val();
                        console.log(shipper);
                        console.log(to_date);
                        console.log(from_date);
                       
                        $.ajax({
                            url: '{!! route('admin.dashboard.userwise.commission.data') !!}',
                            method: 'post',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'search_date_from': from_date,
                                'search_date_to': to_date,
                                'search_shipper': shipper,
                            }
                        }).done(function (data) {
                            if(data.status){
                                $('#booked').text(data.stats.booked);
                                $('#received').text(data.stats.received);
                                $('#revenue').text(data.stats.revenue);
                                $('#commission').text(data.stats.commission);
                                console.log(data);
                                table.draw();

                            }else{
                                $('#booked').text(0);
                                $('#received').text(0);
                                $('#revenue').text(0);
                                $('#commission').text(0);
                                table.draw();
                            }
                            UnblockPagePermanently();
                        }); 
                    // if(admin == null || admin == ''){
                    //     $('#s_datatable_div').addClass('d-none');
                    //     $('#f_datatable_div').removeClass('d-none');
                    //     table.draw(true);
                    // }
                    // else{
                    //     $('#f_datatable_div').addClass('d-none');
                    //     $('#s_datatable_div').removeClass('d-none');
                    //     s_table.draw(true);
                    // }
                }
            });




            // $('#search_filter_btn').on('click',function () {
            //     table.draw();
            // });
            // $('#shipments_booked').on('click', function () {
            //     add_animation($(this));
            //     $('#cards_filter_input').val('booked');
            //     table.draw();
            // });
            // $('#shipments_received').on('click', function () {
            //     add_animation($(this));
            //     $('#cards_filter_input').val('received');
            //     table.draw();
            // });
            // $('#revenue_earned').on('click', function () {
            //     add_animation($(this));
            //     $('#cards_filter_input').val('revenue');
            //     table.draw();
            // });
            // $('#commission_earned').on('click', function () {
            //     add_animation($(this));
            //     $('#cards_filter_input').val('commission');
            //     table.draw();
            // });
           

        });

    </script>
@endsection