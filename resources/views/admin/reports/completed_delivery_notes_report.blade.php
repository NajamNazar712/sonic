@extends('admin.layout.master')

@section('title', 'Completed Delivery Notes Report')

@section('content')
    <h1 class="mb-1">
        Completed Delivery Notes Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div id="search_form" class="row mb-2 justify-content-center">

                    <div class="col-4">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_dn_no" id="search_dn_no" placeholder="Search Delivery Note Number">
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Search Tracking Number">
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_rider" id="search_rider" class="form-control select2">
                                @foreach($riders as $rider)
                                    <option value="{{$rider->id}}">{{$rider->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_assigned_by" id="search_assigned_by" class="form-control select2">
                                @foreach($admins as $admin)
                                    <option value="{{$admin->id}}">{{$admin->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_updated_by" id="search_updated_by" class="form-control select2">
                                @foreach($admins as $admin)
                                    <option value="{{$admin->id}}">{{$admin->name}}</option>
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
                            <input type="text" name="submission_date" class="form-control bg-primary border-primary white rounded-right" id="submission_date" placeholder="Submission Date" data-value="">
                        </div>
                    </div>
                    <div class="col-4">

                        <div class="form-group input-group ml">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Assigned Date (From)">
                        </div>
                    </div>
                    <div class="col-4 ">
                        <div class="form-group input-group ml">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Assigned Date (To)">
                        </div>

                    </div>
                    <div class="col-4">

                        <div class="form-group input-group ml">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="update_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="update_date_from" placeholder="Update Date (From)">
                        </div>
                    </div>
                    <div class="col-4 ">
                        <div class="form-group input-group ml">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="update_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="update_date_to" placeholder="Update Date (To)">
                        </div>

                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Delivery Note No.</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Rider</th>
                        <th class="border-primary border-darken-1">Route</th>
                        <th class="border-primary border-darken-1">No. Of Shipments</th>
                        <th class="border-primary border-darken-1">No. Of Shipments Delivered</th>
                        <th class="border-primary border-darken-1">Assigned By</th>
                        <th class="border-primary border-darken-1">Assigned Date</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Update Date</th>
                        <th class="border-primary border-darken-1">Verified By</th>
                        <th class="border-primary border-darken-1">Verified Date</th>
                        <th class="border-primary border-darken-1">DNCC Amount</th>
                        <th class="border-primary border-darken-1">Aging (Added to Updated)</th>
                        <th class="border-primary border-darken-1">Aging (Updated to Verified)</th>
                        <th class="border-primary border-darken-1">Aging (Added to Verified)</th>
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
    <!--Delivered Shipments popup -->
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
    <!--Shipments popup -->

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

    <style>
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
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #64a0d2;
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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_dn_no,#search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_updated_by').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Assigned By',
                width:'100%',
                allowClear:true
            });
            $('#search_assigned_by').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Created By',
                width:'100%',
                allowClear:true
            });
            $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Rider',
                width:'100%',
                allowClear:true
            });

            var submission_date = $('#submission_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#submission_date_root').css('top','40px');
                },
                onSet: function(context) {
                }
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
            $('#search_form #update_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #update_date_to').pickadate('picker').set('min', $('#search_form #update_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #update_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #update_date_from').pickadate('picker').set('max', $('#search_form #update_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.completed_delivery_notes.list') }}',
                        data: {
                            'page': 'all',
                            'search_dn_no': $('#search_dn_no').val(),
                            'search_tracking': $('#search_tracking_no').val(),
                            'search_rider': $('#search_rider').val(),
                            'search_assigned_by': $('#search_assigned_by').val(),
                            'search_updated_by': $('#search_updated_by').val(),
                            'search_hub': $('#search_hub').val(),
                            'search_submission': $('input[name="submission_date_formatted"]').val(),
                            'search_date_from': $('input[name="search_date_from_formatted"]').val(),
                            'search_date_to': $('input[name="search_date_to_formatted"]').val(),
                            'update_date_from': $('input[name="update_date_from_formatted"]').val(),
                            'update_date_to': $('input[name="update_date_to_formatted"]').val()
                        },
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Delivery Note No.');
                            head.push('Hub');
                            head.push('Rider');
                            head.push('Route');
                            head.push('No Of Shipment(s)');
                            head.push('No Of Shipment(s) Delivered');
                            head.push('Assigned By');
                            head.push('Assigned Date');
                            head.push('Updated By');
                            head.push('Updated Date');
                            head.push('Verified By');
                            head.push('Verified Date');
                            head.push('DNCC Amount');
                            head.push('Aging (Added to Updated)');
                            head.push('Aging (Updated to Verified)');
                            head.push('Aging (Added to Verified)');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.delivery_note);
                                row.push(values.hub);
                                row.push(values.rider);
                                row.push(values.route);
                                row.push(values.shipments_count);
                                row.push(values.delivered_shipments);
                                row.push(values.assignee);
                                row.push(values.created_at);
                                row.push(values.updated_by);
                                row.push(values.status_updated);
                                row.push(values.verified_by);
                                row.push(values.status_verified);
                                row.push(values.amount);
                                row.push(values.aging_create_update);
                                row.push(values.aging_update_verified);
                                row.push(values.aging_create_verified);

                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header:head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '350px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Completed Delivery Notes Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                url: '{{ route('admin.reports.completed_delivery_notes.list') }}',
                data: function (d) {
                    d.search_dn_no = $('#search_dn_no').val();
                    d.search_tracking = $('#search_tracking_no').val();
                    d.search_rider = $('#search_rider').val();
                    d.search_assigned_by = $('#search_assigned_by').val();
                    d.search_updated_by = $('#search_updated_by').val();
                    d.search_hub = $('#search_hub').val();
                    d.search_submission = $('input[name="submission_date_formatted"]').val();
                    d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                    d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    d.update_date_from = $('input[name="update_date_from_formatted"]').val();
                    d.update_date_to = $('input[name="update_date_to_formatted"]').val();
                }
                },
                rowId:'delivery_note_id',
                order: [[10, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'delivery_note_link' ,name: 'delivery_notes.id', class: 'align-middle text-center delivery_note_link'},
                    { data:'hub' ,name: 'oc.name', class: 'align-middle hub'},
                    { data:'rider' ,name: 'riders.name', class: 'align-middle rider'},
                    { data:'route' ,name: 'route', class: 'align-middle route'},
                    { data:'shipments_count_link' ,name: 'delivery_notes.shipments_count', class: 'align-middle shipments_count_link text-center'},
                    { data:'delivered_shipments_link' ,name: 'delivery_notes.delivered_shipments', class: 'align-middle delivered_shipments_link text-center'},
                    { data:'assignee' ,name: 'admins.name', class: 'align-middle assignee'},
                    { data:'created_at' ,name: 'delivery_notes.created_at', class: 'align-middle created_at'},
                    { data:'updated_by' ,name: 'ub.name', class: 'align-middle updated_by'},
                    { data:'status_updated' ,name: 'delivery_notes.status_updated_at', class: 'align-middle updated_at'},
                    { data:'verified_by' ,name: 'vb.name', class: 'align-middle verified_by'},
                    { data:'status_verified' ,name: 'delivery_notes.status_verified_at', class: 'align-middle verified_time'},
                    { data:'amount' ,name: 'delivery_notes.total_cod_amount', class: 'align-middle amount'},
                    {orderable: false, searchable: false, data:'aging_create_update' ,name: 'aging_create_update', class: 'align-middle aging_create_update'},
                    {orderable: false, searchable: false, data:'aging_update_verified' ,name: 'aging_update_verified', class: 'align-middle aging_update_verified'},
                    {orderable: false, searchable: false, data:'aging_create_verified' ,name: 'aging_create_verified', class: 'align-middle aging_create_verified'},
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
            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click','tr td.shipments_count_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.reports.completed_delivery_notes.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'delivery_note_id': id
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
            $('#datatable tbody').on('click','tr td.delivered_shipments_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#delivered_shipments_modal .modal-body').html('');
                $('#delivered_shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.reports.completed_delivery_notes.shipments.delivered') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'delivery_note_id': id
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
                            $('#delivered_shipments_modal .modal-body').html(html);
                        }
                    });

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
            $('#datatable tbody').on('click', 'tr td.delivery_note_link button.print', function() {
                var delivery_note_id = parseInt($(this).parents('tr').attr('id'));

                print(delivery_note_id);
            });
        });
    </script>
@endsection