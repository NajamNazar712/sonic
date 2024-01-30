@extends('admin.layout.master')

@section('title', 'Cargo Manifest Report')

@section('content')
    <h1 class="mb-1">
        Cargo Manifest Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <div class="col-12">
                        <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                            <div class="col-4 mt-1">
                                <fieldset class="form-group">
                                    <select name="select_origin" id="select_origin" class="form-control select2">
                                        @foreach($riders as $rider)
                                            <option value="{{$rider->id}}">{{$rider->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-4 mt-1">
                                <fieldset class="form-group">
                                    <select name="select_destination" id="select_destination" class="form-control select2">
                                        @foreach($hubs as $city)
                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-4 mt-1">
                                <fieldset class="form-group">
                                    <select name="select_sub_segment" id="select_sub_segment" class="form-control select2">
                                        @foreach($zones as $zone)
                                            <option value="{{$zone->id}}">{{$zone->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-4 mt-1">
                                <div class="form-group input-group ">
                                    <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                    </div>
                                    <input type="text" name="search_date_from"
                                           class="form-control pickadate bg-primary border-primary white rounded-right"
                                           id="search_date_from" placeholder="Delivery Note Created Date (From)" title="Arrival Date (From)" data-value="{{ Carbon\Carbon::today() }}">
                                </div>
                            </div>
                            <div class="col-4 mt-1">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                    </div>
                                    <input type="text" name="search_date_to"
                                           class="form-control pickadate bg-primary border-primary white rounded-right"
                                           id="search_date_to" placeholder="Delivery Note Created Date (To)" title="Arrival Date (To)" data-value="{{ Carbon\Carbon::today() }}">
                                </div>
                            </div>

                            <div class="col-2 mt-1">
                                <div class="form-group">
                                    <button type="button" id="search_filter_btn"
                                            class="btn btn-outline-info btn-min-width"><i class="la la-search"></i>
                                        Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <table class="table table-bordered datatable" id="datatable" style="width: 100%;z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1 align-middle" rowspan="2">S.No.</th>
                        <th class="border-primary border-darken-1 align-middle" rowspan="2">Arrived (Up City)</th>
                        <th class="border-primary border-darken-1 align-middle" colspan="2">
                            <div class="text-center">Manifested</div>
                        </th>
                        <th class="border-primary border-darken-1 align-middle" colspan="2">Without Manifest</th>
                        <th class="border-primary border-darken-1 align-middle" colspan="2">Misroute</th>
                    </tr>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"># of Shipments</th>
                        <th class="border-primary border-darken-1">%age</th>
                        <th class="border-primary border-darken-1"># of Shipments</th>
                        <th class="border-primary border-darken-1">%age</th>
                        <th class="border-primary border-darken-1"># of Shipments</th>
                        <th class="border-primary border-darken-1">%age</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!--Shipments popup -->
    <div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_modal_title">Shipment(s)</h4>

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
    <!--Shipments popup -->
    <!--Delivered Shipments Popup -->
    <div class="modal fade" id="delivered_shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="delivered_shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="delivered_shipments_modal_title">Delivered Shipment(s)</h4>

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
    <!--Delivered Shipments Popup -->

    <div class="modal fade" id="app_shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="app_shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="app_shipments_modal_title">Shipment(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <input type="hidden" name="delivery_note_id" id="app_delivery_note_id">
                    <table class="table table-bordered datatable" id="app_shipments_datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking Number</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Status Update Time</th>
                            <th class="border-primary border-darken-1">Rider Update Time</th>
                            <th class="border-primary border-darken-1">Delivered / Undelivered Status</th>
                            <th class="border-primary border-darken-1">Status Reason</th>
                            <th class="border-primary border-darken-1">Received By/Refused By</th>
                            <th class="border-primary border-darken-1">CNIC No.</th>
                            <th class="border-primary border-darken-1">Relation</th>
                            <th class="border-primary border-darken-1">POD</th>
                            <th class="border-primary border-darken-1">CNIC</th>
                            <th class="border-primary border-darken-1">House</th>
                            <th class="border-primary border-darken-1">CCD Slip</th>
                            <th class="border-primary border-darken-1">Audio</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dbf_shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="dbf_shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="dbf_shipments_modal_title">Shipment(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <input type="hidden" name="delivery_note_id" id="dbf_delivery_note_id">
                    <table class="table table-bordered datatable" id="dbf_shipments_datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking Number</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Status Update Time</th>
                            <th class="border-primary border-darken-1">Delivered / Undelivered Status</th>
                            <th class="border-primary border-darken-1">Status Reason</th>
                            <th class="border-primary border-darken-1">Received By/Refused By</th>
                            <th class="border-primary border-darken-1">CNIC</th>
                            <th class="border-primary border-darken-1">Relation</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="picture_modal" data-backdrop="static" role="dialog" aria-labelledby="picture_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="picture_modal_title">Picture</h4>

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

    <div class="modal fade" id="audio_modal" data-backdrop="static" role="dialog" aria-labelledby="audio_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="audio_modal_title">Audio</h4>

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
        table tfoot tr th, table.dataTable tfoot tr th {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }
    </style>

@endsection
@section('js')

    <script src="https://cdn.datatables.net/plug-ins/1.10.22/api/sum().js" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            $('#select_sub_segment').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Zone',
                width:'100%',
                allowClear:true
            });
            $('#select_destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Hub',
                width:'100%',
                allowClear:true
            });
            $('#select_origin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Origin',
                width:'100%',
                allowClear:true
            });
           
            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    // blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.last_mile_app.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No');
                            head.push('Trax IDs');
                            head.push('Rider Name');
                            head.push('Hub');
                            head.push('Zone');
                            head.push('Delivery Date');
                            head.push('Total Shipments');
                            head.push('Before 11');
                            head.push('At 11');
                            head.push('At 12');
                            head.push('At 13');
                            head.push('At 14');
                            head.push('At 15');
                            head.push('At 16');
                            head.push('At 17');
                            head.push('At 18');
                            head.push('At 19');
                            head.push('At 20');
                            head.push('At 21');
                            head.push('At 22');
                            head.push('At 23');
                            head.push('After 23');
                            head.push('Total Updated Shipments');
                            head.push('Update Via App');
                            head.push('Update Via Admin');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.rider_name);
                                row.push(values.hub);
                                row.push(values.zone);
                                row.push(values.delivery_date);
                                row.push(values.total_shipments_excel);
                                row.push(values.before_11_count);
                                row.push(values.at_11_count);
                                row.push(values.at_12_count);
                                row.push(values.at_13_count);
                                row.push(values.at_14_count);
                                row.push(values.at_15_count);
                                row.push(values.at_16_count);
                                row.push(values.at_17_count);
                                row.push(values.at_18_count);
                                row.push(values.at_19_count);
                                row.push(values.at_20_count);
                                row.push(values.at_21_count);
                                row.push(values.at_22_count);
                                row.push(values.at_23_count);
                                row.push(values.after_23_count);
                                row.push(values.total_updated_shipments);
                                row.push(values.updated_via_rider1);
                                row.push(values.updated_via_admin1);

                                body.push(row);
                            });
                        },
                        async: false
                    });
                    return {body: body, header: head,};
                }
            });
            var total_shipments = 0;
            var app_shipments = 0;
            var dbf_shipments = 0;

            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Last Mile App Report',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                autoWidth: true,
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.last_mile_app.list') }}',
                    data: function (d) {
                        d.select_origin = $('#select_origin').val();
                        d.select_origin_cat = $('#select_origin_cat').val();
                        d.select_sub_segment = $('#select_sub_segment').val();
                        d.select_destination = $('#select_destination').val();
                        d.search_dn_no = $('#search_dn_no').val();
                        d.search_tracking = $('#search_tracking_no').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'id',
                order: [[1, 'desc']],
                columns: [
                    {data: 'id',orderable: false, searchable: false, class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'trax_id', name: 'rider_wise_delivery_note_summaries.trax_id', class: 'align-middle text-center trax_id'},
                    {data: 'before_11_count', class: 'align-middle text-center', orderable: false, searchable: false},
                    {data: 'at_11_count',  name:'rider_wise_delivery_note_summaries.at_11_count', class: 'align-middle text-center', orderable: false, searchable: false},
                    {data: 'before_11_count', class: 'align-middle text-center', orderable: false, searchable: false},
                    {data: 'at_11_count',  name:'rider_wise_delivery_note_summaries.at_11_count', class: 'align-middle text-center', orderable: false, searchable: false},
                    {data: 'updated_via_rider', name:'updated_via_rider', class: 'align-middle text-center update_via_app', orderable: false, searchable: false},
                    {data: 'updated_via_admin', name:'updated_via_admin', class: 'align-middle text-center update_via_dbf', orderable: false, searchable: false},
                    // {data: 'updated_via_admin', name:'updated_via_admin', class: 'align-middle text-center update_via_dbf', orderable: false, searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    if(index == 0){

                        total_shipments = data.total_shipments;
                        app_shipments = data.updated_via_rider1;
                        dbf_shipments = data.updated_via_admin1;
                    }
                    else{
                        total_shipments += data.total_shipments;
                        app_shipments += data.updated_via_rider1;
                        dbf_shipments += data.updated_via_admin1;

                    }
                    if(index == (info.end - 1)){
                        $('#total_shipments').text(total_shipments);
                        $('#app_shipments').text(app_shipments);
                        $('#dbf_shipments').text(dbf_shipments);
                    }
                },
                drawCallback: function () {
                    var api = this.api();

                    var update_via_app_count = 0;
                    var update_via_dbf_count = 0;
                    var total_shipment_count = 0;
                    api.rows( {page:'current'} ).every( function () {
                        update_via_app_count+=this.data().updated_via_rider1;
                        update_via_dbf_count+=this.data().updated_via_admin1;
                        total_shipment_count+=this.data().total_shipments;
                    } );

                    // setTimeout(function(){
                    //     document.getElementsByClassName('total_shipment_count')[0].innerHTML=total_shipment_count;
                    //     document.getElementsByClassName('update_via_app_count')[0].innerHTML=update_via_app_count;
                    //     document.getElementsByClassName('update_via_dbf_count')[0].innerHTML=update_via_dbf_count;
                    // }, 1000);

                },
                stateLoaded: function (settings, data) {
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });


            $('#search_filter_btn').on('click',function () {
                $('#total_shipments').text(0);
                $('#app_shipments').text(0);
                $('#dbf_shipments').text(0);
                table.draw(true);
            });

            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click','tr td.total_shipments_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.reports.last_mile_app.shipment_list') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var html = '';

                            if (data.shipments) {
                                $.each(data.shipments, function(index, tracking_number) {
                                    html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                });
                            }
                            $('#shipments_modal .modal-body').html(html);
                        }
                    });
            });

            var app_table;
            $('#datatable tbody').on('click','tr td.update_via_app button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));

                if(id){
                    $('#app_delivery_note_id').val(id);
                    $('#app_shipments_modal').modal('show');


                    app_table = $('#app_shipments_datatable').DataTable({
                        dom: 't',
                        buttons: [
                            {
                                extend: 'excelHtml5',
                                title: 'Shipments',
                                text:'<i class="la la-file-excel-o"></i> Excel',
                            },
                        ],
                        "autoWidth": true,
                        pageLength: -1,
                        processing: true,
                        language: {
                            processing: data_table_loader
                        },
                        ajax: {
                            url: '{{ route('admin.reports.last_mile_app.app_shipments_list') }}',
                            data: function (d) {
                                d.id = $('#app_delivery_note_id').val();
                            }
                        },
                        rowId: 'shipment_id',
                        order: [[3, 'desc']],
                        columns: [
                            {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                            { data:'tracking_number_link' ,name: 's.tracking_number', class: 'align-middle tracking_number_link'},
                            { data:'status' ,name: 'status', class: 'align-middle status',orderable: false, searchable: false},
                            { data:'update_date_time' ,name: 'shipments_journey.created_at', class: 'align-middle update_date_time'},
                            { data:'rider_time' ,name: 'rider_deliveries.added_at', class: 'align-middle rider_time'},
                            { data:'shipment_status' ,name: 'ss.name', class: 'align-middle shipment_status'},
                            { data:'shipment_reason' ,name: 'ssr.name', class: 'align-middle shipment_reason'},
                            { data:'received_or_refused_by' ,name: 'received_or_refused_by', class: 'align-middle received_or_refused_by'},
                            { data:'cnic' ,name: 'rider_deliveries.cnic', class: 'align-middle cnic'},
                            { data:'relation' ,name: 'rider_deliveries.relation', class: 'align-middle relation'},
                            { data:'pod' ,name: 'pod', class: 'align-middle pod',orderable: false, searchable: false},
                            { data:'cnic_image' ,name: 'cnic_image', class: 'align-middle cnic_image',orderable: false, searchable: false},
                            { data:'house_image' ,name: 'house_image', class: 'align-middle house_image',orderable: false, searchable: false},
                            { data:'ccd_image' ,name: 'ccd_image', class: 'align-middle ccd_image',orderable: false, searchable: false},
                            { data:'audio_path' ,name: 'rider_deliveries.audio_path', class: 'align-middle audio_path',orderable: false, searchable: false},

                        ],
                        rowCallback: function(row, data, index) {
                            var info = app_table.page.info();
                            $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                        },
                        initComplete: function() {
                            this.api().table().columns.adjust();
                        }
                    });
                }

            });

            var dbf_table;

            $('#datatable tbody').on('click','tr td.update_via_dbf button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id){
                    $('#dbf_delivery_note_id').val(id);
                    $('#dbf_shipments_modal').modal('show');

                    dbf_table = $('#dbf_shipments_datatable').DataTable({
                        dom: 't',
                        // scrollX: true,
                        buttons: [
                            {
                                extend: 'excelHtml5',
                                title: 'Shipments',
                                text:'<i class="la la-file-excel-o"></i> Excel',
                            },
                        ],
                        "autoWidth": true,
                        pageLength: -1,
                        processing: true,
                        language: {
                            processing: data_table_loader
                        },
                        ajax: {
                            url: '{{ route('admin.reports.last_mile_app.dbf_shipments_list') }}',
                            data: function (d) {
                                d.id = $('#dbf_delivery_note_id').val();
                            }
                        },
                        rowId: 'shipment_id',
                        order: [[3, 'desc']],
                        columns: [
                            {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                            { data:'tracking_number_link' ,name: 's.tracking_number', class: 'align-middle tracking_number_link'},
                            { data:'status' ,name: 'status', class: 'align-middle status',orderable: false, searchable: false},
                            { data:'update_date_time' ,name: 'shipments_journey.created_at', class: 'align-middle update_date_time'},
                            { data:'shipment_status' ,name: 'ss.name', class: 'align-middle shipment_status'},
                            { data:'shipment_reason' ,name: 'ssr.name', class: 'align-middle shipment_reason'},
                            { data:'received_or_refused_by' ,name: 'received_or_refused_by', class: 'align-middle received_or_refused_by'},
                            { data:'consignee_cnic' ,name: 'consignee_cnic', class: 'align-middle consignee_cnic'},
                            { data:'consignee_relation' ,name: 'consignee_relation', class: 'align-middle consignee_relation'},

                        ],
                        rowCallback: function(row, data, index) {
                            var info = dbf_table.page.info();
                            $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                        },
                        initComplete: function() {
                            this.api().table().columns.adjust();
                        }
                    });
                }

            });


            $('#app_shipments_modal').on('hidden.bs.modal', function () {
                $('#app_delivery_note_id').val('');
                app_table.clear();
                app_table.destroy();
            });

            $('#dbf_shipments_modal').on('hidden.bs.modal', function () {
                $('#dbf_delivery_note_id').val('');
                dbf_table.clear();
                dbf_table.destroy();
            });

            var route = '{!! route('admin.tracking.index') !!}';

            $('body').on('click','#app_shipments_datatable tbody tr td.pod button',function () {
                var link = $(this).attr('data-link');

                var image = '<img src="' + link + '" style="width: 100%; max-width: 200px;" />';

                $('#picture_modal .modal-body').html(image);

                $('#picture_modal').modal('show');
            });

            $('body').on('click','#app_shipments_datatable tbody tr td.cnic_image button',function () {
                var link = $(this).attr('data-link');

                var image = '<img src="' + link + '" style="width: 100%; max-width: 200px;" />';

                $('#picture_modal .modal-body').html(image);

                $('#picture_modal').modal('show');
            });

            $('body').on('click','#app_shipments_datatable tbody tr td.house_image button',function () {
                var link = $(this).attr('data-link');

                var image = '<img src="' + link + '" style="width: 100%; max-width: 200px;" />';

                $('#picture_modal .modal-body').html(image);

                $('#picture_modal').modal('show');
            });

            $('body').on('click','#app_shipments_datatable tbody tr td.ccd_image button',function () {
                var link = $(this).attr('data-link');

                var image = '<img src="' + link + '" style="width: 100%; max-width: 200px;" />';

                $('#picture_modal .modal-body').html(image);

                $('#picture_modal').modal('show');
            });

            $('body').on('click','#app_shipments_datatable tbody tr td.audio_path button',function () {
                var link = $(this).attr('data-link');

                var audio = '<audio controls id="sound"> <source src="' + link + '" type="audio/mp4"  > </audio>';

                $('#audio_modal .modal-body').html(audio);

                $('#audio_modal').modal('show');
            });


            function print(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }
            $('#datatable tbody').on('click', 'tr td.delivery_note button.print', function() {
                var delivery_note_id = parseInt($(this).parents('tr').attr('id'));

                print(delivery_note_id);
            });

            $('#audio_modal').on('hide.bs.modal', function (e) {
                $('audio#sound')[0].pause();
                $('audio#sound')[0].currentTime = 0;
            });
        });

    </script>
@endsection