@extends('admin.layout.master')

@section('title', 'Rider-Picked Status Report (W/O Arrival)')

@section('content')
    <h1 class="mb-1">
        Rider-Picked Status Report (W/O Arrival)
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div id="search_form" class="row mb-2 justify-content-center">
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_hub" id="search_hub" class="form-control select2">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-5">
                        <fieldset class="form-group">
                            <select name="search_rider" id="search_rider" class="form-control select2">
                                @foreach($riders as $rider)
                                    <option value="{{$rider->id}}">{{$rider->name}}{{ $rider->trax_id != null ? "|".$rider->trax_id : " " }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-5">
                        <fieldset class="form-group">
                            <select name="search_shipper" id="search_shipper" class="form-control select2">
                                @foreach($shippers as $shipper)
                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <div id="datatable_wrapper">
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking Number</th>
                            <th class="border-primary border-darken-1">Rider Trax ID</th>
                            <th class="border-primary border-darken-1">Rider Name</th>
                            <th class="border-primary border-darken-1">Status Date</th>
                            <th class="border-primary border-darken-1">Rider City</th>
                            <th class="border-primary border-darken-1">Rider Hub</th>
                            <th class="border-primary border-darken-1">Rider Zone</th>
                            <th class="border-primary border-darken-1">Shipper Name</th>
                            <th class="border-primary border-darken-1">Shipper Address</th>
                            <th class="border-primary border-darken-1">Shipper City</th>
                            <th class="border-primary border-darken-1">Origin</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Cod Amount</th>
                            <th class="border-primary border-darken-1">Product Type</th>
                           {{-- <thclass="border-primaryborder-darken-1">Remarks</th> --}}
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_modal_title"></h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <style type="text/css">
        table.dataTable {
            font-size: 12px;
        }

        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
            word-wrap: break-word;
        }

        table.dataTable thead tr th:before,
        table.dataTable thead tr th:after {
            height: 20px;
            margin-bottom: -10px;
            bottom: 50% !important;
        }

        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }
    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#datatable_wrapper').hide();

            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });
            
            $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Rider',
                width:'100%',
                allowClear:true
            });

            $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Shipper',
                width:'100%',
                allowClear:true
            });

            var max = '{{ Carbon\Carbon::now() }}';

            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: max,
                format:'dd mmmm, yyyy',
                // format: 'yyyy-mm-dd',
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
                max: max,
                format:'dd mmmm, yyyy',
                // format: 'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #from_date').pickadate('picker').set('min', $('#search_form #to_date').pickadate('picker').get('select'));
                    }
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.rider_picked.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            footer = [];
                            head.push('S.No');
                            head.push('Tracking Number');
                            head.push('Rider Trax ID');
                            head.push('Rider Name');
                            head.push('Status Date');
                            head.push('Rider City');
                            head.push('Rider Hub');
                            head.push('Rider Zone');
                            head.push('Shipper Name');
                            head.push('Shipper Address');
                            head.push('Shipper City');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Cod Amount');
                            head.push('Product Type');

                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.rider_trax_id);
                                row.push(values.rider_name);
                                row.push(values.status_date);
                                row.push(values.rider_city);
                                row.push(values.rider_hub);
                                row.push(values.rider_zone);
                                row.push(values.shipper_name);
                                row.push(values.shipper_address);
                                row.push(values.shipper_cities);
                                row.push(values.origin);
                                row.push(values.shipper_destination);
                                row.push(values.cod);
                                row.push(values.product_type);
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );

            // $('#datatable').append("<tfoot><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>");
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Rider-Picked Status Report (W/O Arrival)',
                        className: 'btn btn-primary excel',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                        footer: true
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                autoWidth:false,
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.reports.rider_picked.list') }}',
                    data: function (d) {
                        d.search_hub = $('#search_hub').val();
                        d.search_rider = $('#search_rider').val();
                        d.search_shipper = $('#search_shipper').val();
                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                order: [[4, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number_link' ,name: 'shipments.tracking_number', class: 'align-middle text-center tracking_number_link'},
                    { data:'rider_trax_id' ,name: 'r.trax_id', class: 'align-middle text-center rider_trax_id'},
                    { data:'rider_name' ,name: 'r.name', class: 'align-middle text-center rider_name'},
                    { data:'status_date' ,name: 'sj.updated_at', class: 'align-middle text-center status_date'},
                    { data:'rider_city' ,name: 'rc.name', class: 'align-middle text-center rider_city'},
                    { data:'rider_hub' ,name: 'rh.name', class: 'align-middle text-center rider_hub'},
                    { data:'rider_zone' ,name: 'rz.name', class: 'align-middle text-center rider_zone'},
                    { data:'shipper_name' ,name: 'u.name', class: 'align-middle text-center shipper_name'},
                    { data:'shipper_address' ,name: 'u.address', class: 'align-middle text-center shipper_address'},
                    { data:'shipper_cities' ,name: 'uc.name', class: 'align-middle text-center shipper_cities'},
                    { data:'origin' ,name: 'uo.name', class: 'align-middle text-center origin'},
                    { data:'shipper_destination' ,name: 'des.name', class: 'align-middle text-center shipper_destination'},
                    { data:'cod' ,name: 'shipments.amount', class: 'align-middle text-center cod'},
                    { data:'product_type' ,name: 'p.product_name', class: 'align-middle text-center product_type'},
                    // { data:'rider_city' ,name: 'rc.name', class: 'align-middle text-center origin'},

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                },
                // footerCallback: function(row, data, start, end, display) {
                //     var scanned_shipments_count = 0;
                //     var arrived_shipments_count = 0;
                //     var without_scan_shipments_count = 0;
                    
                //     $.each(data, function(index, shipment_data) {
                //         scanned_shipments_count += shipment_data.total_shipments;
                //         arrived_shipments_count += shipment_data.total_arrived_shipments;
                //         without_scan_shipments_count += shipment_data.without_scan_shipments;
                //     });
                //     var api = this.api();
                //     // api.columns('.date', {
                //     //     page: 'current'
                //     // }).every(function() {
                //     //     $(this.footer()).html('Total');
                //     // });
                    
                //     api.columns('.scanned_shipments', {
                //         page: 'current'
                //     }).every(function() {
                        
                //         $(this.footer()).html(scanned_shipments_count);
                //     });
                //     api.columns('.arrived_shipments', {
                //         page: 'current'
                //     }).every(function() {
                //         $(this.footer()).html(arrived_shipments_count);
                //     });
                //     api.columns('.without_scan_shipments', {
                //         page: 'current'
                //     }).every(function() {
                       
                //         $(this.footer()).html(without_scan_shipments_count);
                //     });
                // }
            });

            $('#search_filter_btn').on('click',function () {
                $('#datatable_wrapper').show();
                table.draw();
            });

            // $('#datatable tbody').on('click', 'tr td.pickup_note button.print', function() {
                   
            //     var pickup_note_id = parseInt($(this).attr('rel'));
            //     print(pickup_note_id);
            // });
    
            // function print(id) {
            //     $.ajax({
            //         url: '{!! route('admin.v2_pickups.pending.print') !!}',
            //         method: 'POST',
            //         data: {
            //             'ids': [id],
            //             '_token': '{{ csrf_token() }}'
            //         }
            //     })
            //     .done(function(data) {
            //         var tab = window.open('', '_blank');
    
            //         if(!tab) {
            //             swal({
            //                 title: 'Popup Blocker Enabled!',
            //                 text: 'Please add this site to your exception list.',
            //                 icon: 'error',
            //                 closeOnClickOutside: false,
            //                 closeOnEsc: false
            //             });
            //         }
            //         else {
            //             tab.document.write(data);
            //             tab.document.close();
            //             tab.focus();
            //         }
            //     });
            // }
        });
    </script>
@endsection