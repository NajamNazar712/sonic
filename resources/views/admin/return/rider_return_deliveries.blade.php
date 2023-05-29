@extends('admin.layout.master')

@section('title', 'Return Deliveries')

@section('content')
    <h1 class="mb-1">
        Return Deliveries
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S.No.</th>
                        <th class="border-primary border-darken-1">Return Note#</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Zone</th>
                        <th class="border-primary border-darken-1">Return Note Date</th>
                        <th class="border-primary border-darken-1">Rider Id</th>
                        <th class="border-primary border-darken-1">Rider Name</th>
                        <th class="border-primary border-darken-1">Total Shipment</th>
                        <th class="border-primary border-darken-1">Update Via App</th>
                        <th class="border-primary border-darken-1">Update Via DBF</th>

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
                    <input type="hidden" name="return_note_id" id="app_return_note_id">
                    <table class="table table-bordered datatable" id="app_shipments_datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking Number</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Status Update Time</th>
                            <th class="border-primary border-darken-1">Delivered / Undelivered Status</th>
                            <th class="border-primary border-darken-1">Status Reason</th>
                            <th class="border-primary border-darken-1">Received By/Refused By</th>
                            <th class="border-primary border-darken-1">Picture</th>
                            <th class="border-primary border-darken-1">POD</th>
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
                    <input type="hidden" name="return_note_id" id="dbf_return_note_id">
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

            $('#search_zone').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Zone',
                width:'100%',
                allowClear:true
            });
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Hub',
                width:'100%',
                allowClear:true
            });
            $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Rider',
                width:'100%',
                allowClear:true
            });
            $('#search_dn_no,#search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
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

            $('#search_form #search_update_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_update_date_to').pickadate('picker').set('min', $('#search_form #search_update_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #search_update_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_update_date_from').pickadate('picker').set('max', $('#search_form #search_update_date_to').pickadate('picker').get('select'));
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
                        url: '{{ route('admin.return.return_deliveries.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            footer = [];

                            head.push('S. No');
                            head.push('Return Note#');
                            head.push('Hub');
                            head.push('Zone');
                            head.push('Return Note Data');
                            head.push('Rider Name');
                            head.push('Total Shipment');
                            head.push('Update Via App');
                            head.push('Update Via DBF');
                            var total_shipments_count = 0;
                            var update_via_app_count = 0;
                            var update_via_dbf_count = 0;
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.return_note_id_padded);
                                row.push(values.city);
                                row.push(values.zone);
                                row.push(values.created_at);
                                row.push(values.rider);
                                row.push(values.total_shipments);
                                row.push(values.shipments_rider_updated);
                                row.push(values.shipments_dbf_updated);

                                body.push(row);
                                total_shipments_count+=values.total_shipments
                                update_via_app_count+=values.shipments_rider_updated
                                update_via_dbf_count+=values.shipments_dbf_updated
                            });


                            footer.push('');
                            footer.push('Total');
                            footer.push('-');
                            footer.push('-');
                            footer.push('-');
                            footer.push('-');
                            footer.push(total_shipments_count);
                            footer.push(update_via_app_count);
                            footer.push(update_via_dbf_count);
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head, footer: footer};
                }
            });
            var total_shipments = 0;
            var app_shipments = 0;
            var dbf_shipments = 0;


            $('#datatable').append("<tfoot><tr><th colspan='6'>Total:</th><th class='total_shipment_count'></th><th class='update_via_app_count'></th><th class='update_via_dbf_count'></th></tr></tfoot>");
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Rider Return Deliveries',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                        footer: true

                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                autoWidth: true,
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.return.return_deliveries.list') }}',
                    data: function (d) {

                        d.search_rider = $('#search_rider').val();
                        d.search_zone = $('#search_zone').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_dn_no = $('#search_dn_no').val();
                        d.search_tracking = $('#search_tracking_no').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.search_update_date_from = $('input[name="search_update_date_from_formatted"]').val();
                        d.search_update_date_to = $('input[name="search_update_date_to_formatted"]').val();

                    }
                },
                rowId: 'return_note_id',
                order: [[4, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'return_note', name: 'return_notes.id', class: 'align-middle text-center return_note'},
                    {data: 'city', name: 'c.name', class: 'align-middle text-center city'},
                    {data: 'zone', name: 'z.name', class: 'align-middle text-center zone'},
                    {data: 'created_at', name: 'return_notes.created_at', class: 'align-middle text-center created_at'},
                    {data: 'riderid', name: 'r.trax_id ', class: 'align-middle text-center riderid'},
                    {data: 'rider', name: 'r.name', class: 'align-middle text-center rider'},
                    {data: 'total_shipments_link', name: 'return_notes.shipments_count', class: 'align-middle text-center total_shipments_link'},
                    {data: 'update_via_app', name: 'shipments_rider_updated', class: 'align-middle text-center update_via_app', orderable: false, searchable: false},
                    {data: 'update_via_dbf', name:'update_via_dbf', class: 'align-middle text-center update_via_dbf', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    if(index == 0){

                        total_shipments = data.total_shipments;
                        app_shipments = data.shipments_rider_updated;
                        dbf_shipments = data.shipments_dbf_updated;
                    }
                    else{
                        total_shipments += data.total_shipments;
                        app_shipments += data.shipments_rider_updated;
                        dbf_shipments += data.shipments_dbf_updated;

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
                        // console.table(this.data());
                        // console.log('updated_via_App ',this.data().update_via_app);
                        // console.log('shipments_dbf_updated ',this.data().shipments_dbf_updated);
                        // console.log('total_shipments ',this.data().total_shipments);
                        update_via_app_count+=this.data().shipments_rider_updated;
                        update_via_dbf_count+=this.data().shipments_dbf_updated;
                        total_shipment_count+=this.data().total_shipments;

                    } );
                    // console.log(total_delivered_count);
                    // console.log(total_shipment_count);

                    setTimeout(function(){
                        document.getElementsByClassName('total_shipment_count')[0].innerHTML=total_shipment_count;
                        document.getElementsByClassName('update_via_app_count')[0].innerHTML=update_via_app_count;
                        document.getElementsByClassName('update_via_dbf_count')[0].innerHTML=update_via_dbf_count;
                    }, 1000);

                },
                stateLoaded: function (settings, data) {
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control"></select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.total_shipments_link') || $(header).is('.update_via_app') || $(header).is('.update_via_dbf')) {
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
                    url: '{!! route('admin.return.history.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'return_note_id': id
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
                    $('#app_return_note_id').val(id);
                    $('#app_shipments_modal').modal('show');


                    app_table = $('#app_shipments_datatable').DataTable({
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
                        // lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                        pageLength: -1,
                        // pagingType: 'full_numbers',
                        processing: true,
                        language: {
                            processing: data_table_loader
                        },
                        ajax: {
                            url: '{{ route('admin.return.return_deliveries.app_shipment_list') }}',
                            data: function (d) {
                                d.return_note_id = $('#app_return_note_id').val();
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
                            { data:'pod' ,name: 'pod', class: 'align-middle pod',orderable: false, searchable: false},
                            { data:'pod_image' ,name: 'pod_image', class: 'align-middle pod_image',orderable: false, searchable: false},
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
                    $('#dbf_return_note_id').val(id);
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
                        // lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                        pageLength: -1,
                        // pagingType: 'full_numbers',
                        processing: true,
                        language: {
                            processing: data_table_loader
                        },
                        ajax: {
                            url: '{{ route('admin.return.return_deliveries.dbf_shipment_list') }}',
                            data: function (d) {
                                d.return_note_id = $('#dbf_return_note_id').val();
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
                $('#app_return_note_id').val('');
                app_table.clear();
                app_table.destroy();
            });

            $('#dbf_shipments_modal').on('hidden.bs.modal', function () {
                $('#dbf_return_note_id').val('');
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

            $('body').on('click','#app_shipments_datatable tbody tr td.pod_image button',function () {
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
                    url: '{!! route('admin.return.receive.rn.print') !!}',
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
            $('#datatable tbody').on('click', 'tr td.return_note button.print', function() {
                var return_note_id = parseInt($(this).parents('tr').attr('id'));

                print(return_note_id);
            });

            $('#audio_modal').on('hide.bs.modal', function (e) {
                $('audio#sound')[0].pause();
                $('audio#sound')[0].currentTime = 0;
            });
        });

    </script>
@endsection