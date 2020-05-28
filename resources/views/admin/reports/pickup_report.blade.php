@extends('admin.layout.master')

@section('title', 'Pickup Report')

@section('content')
    <h1 class="mb-1">
        Pickup Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-1 justify-content-center">
                    <div class="col-12 ">
                        <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                        <div class="col-8">
                            <div  class="row">   
                                <div class="col-4">
                                    <div class="form-group pb-1">
                                        <select name="department" class="select2" id="department" data-rule-required="true" data-msg-required="Shipper is required">
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group pb-1">
                                        <select name="salesperson" class="select2" id="salesperson" data-rule-required="true" data-msg-required="Shipper is required">
                                        @foreach($salespersons as $salesperson)  
                                            <option value="{{ $salesperson->name }}">{{ $salesperson->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group pb-1">
                                        <select name="origin" class="select2" id="origin" data-rule-required="true" data-msg-required="Shipper is required">
                                        @foreach($origins as $origin)
                                            <option value="{{ $origin->id }}">{{ $origin->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group pb-1">
                                        <select name="category" class="select2" id="category" data-rule-required="true" data-msg-required="Shipper is required">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->name }}">{{ $category->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group pb-1">
                                        <select name="cut_off_time" class="select2" id="cut_off_time" data-rule-required="true" data-msg-required="Shipper is required">
                                            
                                        <option value="">Select Me</option>
                                        
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
                                        <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-rule-required="true" data-msg-required="Date(From) is required" data-value="">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                        </div>
                                        <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-rule-required="true" data-msg-required="Date(To) is required" data-value="">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>

                            </div>
                        </div>
                        <div class="col-4"> 
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">Legend</legend>
                                <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li><span style='background:#228B22;'></span>Pickup Request Picked.Shipment Difference < 10%</li>
                                    <li><span style='background:#98FB98;'></span>Pickup Request Picked.Shipment Difference > 10%</li>
                                    <li><span style='background:#FFDEAD;'></span>Pickup Request Attempted & Not Picked</li>
                                    <li><span style='background:#D3D3D3;'></span>Pickup Request Cancelled</li>
                                    <li><span style='background:#FA8072;'></span>Pickup Request Attempt Failed</li>
                                </ul>
                                </div>
                            </fieldset>
                        </div>
                        </form>
                    </div>
                </div>
                <div class="" id="report_data">
                <h2 class="mb-1">
                    Summary
                </h2>
                    <div class="row">
                        <input type="hidden" id="cards_filter_input">
                        <div class="col-3">
                            <div class="card pull-up">
                                <div class="card-content border rounded" id="shipments_booked">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-left">
                                                <span>Total</span>
                                            </div>
                                            <div class="media-body text-right">
                                                <h3 id="booked">0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card pull-up">
                                <div class="card-content border rounded" id="shipments_booked">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-left">
                                                <span>Pending Operations</span>
                                            </div>
                                            <div class="media-body text-right">
                                                <h3 id="booked">0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card pull-up">
                                <div class="card-content border rounded" id="shipments_booked">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-left">
                                                <span>Pending Sales</span>
                                            </div>
                                            <div class="media-body text-right">
                                                <h3 id="booked">0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card pull-up">
                                <div class="card-content border rounded" id="shipments_booked">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-left">
                                                <span>Before Cut-off</span>
                                            </div>
                                            <div class="media-body text-right">
                                                <h3 id="booked">0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card pull-up">
                                <div class="card-content border rounded" id="shipments_booked">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-left">
                                                <span>After Cut-off</span>
                                            </div>
                                            <div class="media-body text-right">
                                                <h3 id="booked">0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card pull-up">
                                <div class="card-content border rounded" id="shipments_booked">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-left">
                                                <span>Attempted & Picked</span>
                                            </div>
                                            <div class="media-body text-right">
                                                <h3 id="booked">0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card pull-up">
                                <div class="card-content border rounded" id="shipments_booked">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-left">
                                                <span>Attempted & Not Picked</span>
                                            </div>
                                            <div class="media-body text-right">
                                                <h3 id="booked">0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card pull-up">
                                <div class="card-content border rounded" id="shipments_booked">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-left">
                                                <span>Attempted Failed</span>
                                            </div>
                                            <div class="media-body text-right">
                                                <h3 id="booked">0</h3>
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
                                <th class="border-primary border-darken-1">Request ID</th>
                                <th class="border-primary border-darken-1">Reuested Date</th>
                                <th class="border-primary border-darken-1">Status</th>
                                <th class="border-primary border-darken-1">Shipper</th>
                                <th class="border-primary border-darken-1">Salesperson</th>
                                <th class="border-primary border-darken-1">Expected Shipment(s)</th>
                                <th class="border-primary border-darken-1">Received Shipments</th>
                                <th class="border-primary border-darken-1">Shipments Difference</th>
                                <th class="border-primary border-darken-1">Department</th>
                                <th class="border-primary border-darken-1">Attempt Date/Time</th>
                                <th class="border-primary border-darken-1">Trax Reason</th>
                                <th class="border-primary border-darken-1">Trax Remarks</th>
                                <th class="border-primary border-darken-1">Attempt Count</th>
                                <th class="border-primary border-darken-1">Contact Person</th>
                                <th class="border-primary border-darken-1">Vendor</th>
                                <th class="border-primary border-darken-1">Contact No(s)</th>
                                <th class="border-primary border-darken-1">Address</th>
                                <th class="border-primary border-darken-1">City</th>

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
        fieldset.scheduler-border {
            border: 1px groove #ddd !important;
             padding: 0 1.4em 1.4em 1.4em !important;
            margin: 0 0 1.5em 0 !important;
            width:100%;
            -webkit-box-shadow:  0px 0px 0px 0px #000;
                    box-shadow:  0px 0px 0px 0px #000;
        }

        legend.scheduler-border {
            width:auto; 
            border-bottom:none;
        }
        fieldset.scheduler-border .legend-scale ul {
            margin: 0;
            margin-bottom: 10px;
            padding: 0;
            float: left;
            list-style: none;
        }
        fieldset.scheduler-border .legend-scale ul li {
            font-size: 100%;
            list-style: none;
            margin-left: 0;
            line-height: 18px;
            margin-bottom: 3px;
        }
        fieldset.scheduler-border ul.legend-labels li span {
            display: block;
            float: left;
            height: 18px;
            width: 40px;
            margin-right: 5px;
            margin-left: 0;
            /* border: 1px solid #999; */
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
            $('#search_form #department').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Department',
                allowClear:true
            });
            $('#search_form #salesperson').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select SalesPerson',
                allowClear:true
            });
            $('#search_form #origin').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Origin',
                allowClear:true
            });
            $('#search_form #category').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Category',
                allowClear:true
            });
            $('#search_form #shipper').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Shipper',
                allowClear:true
            });
           
           

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.dashboard.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Account ID');
                            head.push('Shipper Name');
                            head.push('Shipment Booked');
                            head.push('Shipment Received');
                            head.push('Revenue');
                            head.push('Commission');
                            head.push('Commission Amount');
                           

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.account_id);
                                row.push(values.shipper);
                                row.push(values.booked);
                                row.push(values.received);
                                row.push(values.revenue);
                                row.push(values.commission);
                                row.push(values.commission_amount);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header:head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Sales Commission Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                scrollX: true, scrollY: '500px',
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
                    method: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.search_shipper = $('#shipper').val();
                       // d.cards_filter = $('#cards_filter_input').val();
                       d.search_date_from = $('input[name="from_date_formatted"]').val();
                       d.search_date_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                order: [[1, 'desc']],
                // columns: [
                //     {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                //     { data:'account_id' ,name: 'u.id', class: 'align-middle text-center account_id'},
                //     { data:'shipper' ,name: 'u.name', class: 'align-middle shipper'},
                //     { data:'booked' ,name: 'booked', class: 'align-middle booked'},
                //     { data:'received' ,name: 'received', class: 'align-middle received'},
                //     { data:'revenue' ,name: 'revenue', class: 'align-middle revenue'},
                //     { data:'commission' ,name: 's.commission', class: 'align-middle commission'},
                //     { data:'commission_amount' ,name: 'commission_amount', class: 'align-middle commission_amount'},
                    
                // ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                   // this.api().table().columns.adjust();
                   var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.booked')  || $(header).is('.received') || $(header).is('.revenue') || $(header).is('.commission') || $(header).is('.commission_amount') || $(header).is('.counts')) {
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
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