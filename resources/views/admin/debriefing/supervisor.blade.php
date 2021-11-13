
@extends('admin.layout.master')
@section('title','Supervisor Dashboard')

@section('content')
    <h1 class="mb-1">
        Supervisor Dashboard
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row justify-content-center">
                    <div class="col-3">
                        <div class="card bg-gradient-agent_assigned_shipments pull-up">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="icon-user text-white font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-white text-right">
                                            <h3 class="white">{{$agent_calls_assigned_count}}</h3>
                                            <span>Agent Assigned Shipment(s)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card bg-gradient-bot_assigned_shipments pull-up">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="icon-envelope text-white font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-white text-right">
                                            <h3 class="text-white">{{$bot_sms_count}}</h3>
                                            <span>Bot SMS Shipment(s)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Delivery Note No.</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Rider</th>
                        <th class="border-primary border-darken-1">No. Of Shipments</th>
                        <th class="border-primary border-darken-1">No. Of Delivered Shipments</th>
                        <th class="border-primary border-darken-1">No. Of Un-Delivered Shipments</th>
                        <th class="border-primary border-darken-1">No. Of Pending Shipments</th>
                        <th class="border-primary border-darken-1">Target Cash</th>
                        <th class="border-primary border-darken-1">Pending Cash Collection</th>
                        <th class="border-primary border-darken-1">Assigned Agent</th>
                        <th class="border-primary border-darken-1">Fake Status Count</th>
                        <th class="border-primary border-darken-1">Caller agent Call Ratio</th>
                        <th class="border-primary border-darken-1">Received delivery Verify Status Call Ratio</th>
                        <th class="border-primary border-darken-1"></th>
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

    <div class="modal fade text-left" id="AssignAgentModal" data-backdrop="static" role="dialog" aria-labelledby="AssignAgentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Assign Agent</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="assign_agent_form" class="form-horizontal" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="delivery_note_id" id="delivery_note_id_input">
                        <div class="row justify-content-center">
                            <div class="col-6 form-group">
                                <label for="hub_id">Hub</label>

                                <select class="form-control hub_id" name="hub_id" id="hub_id" data-rule-required="true" data-msg-required="Hub is required">
                                    @foreach($hubs as $hub)
                                        <option value="{{$hub->id}}">{{$hub->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row justify-content-center">
                            <div class="col-6 form-group d-none" id="agend_input">
                                <label for="end_point_id">Agents</label>
                                <select class="form-control" name="agent_id" id="assign_agent_id" data-rule-required="true" data-msg-required="Agent is required">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="AssignAgentBtn" type="submit" class="btn btn-info">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Shipments popup -->
    <div class="modal fade" id="shipments_sms_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_sms_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_sms_modal_title">Send BOT SMS</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="send_sms_form">
                <div class="modal-body text-center">

                        @csrf
                    <input type="hidden" name="delivery_note_id" id="sms_delivery_note_id">
                        <div id="sms_undelivered_shipments"></div>


                </div>
                <div class="modal-footer">
                    <button id="send_sms_submit" type="submit" class="btn btn-info">Send SMS</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!--Shipments popup -->
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
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

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }
        .bg-gradient-bot_assigned_shipments {
            background-image: linear-gradient(45deg, #5e187b, #ed86ff);
            background-repeat: repeat-x;
        }
        .bg-gradient-agent_assigned_shipments {
            background-image: linear-gradient(45deg, #535BE2, #9ea5ff);
            background-repeat: repeat-x;
        }


    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            
            $('#assign_agent_id').prepend('<option value="" selected="selected"></option>').select2({
				placeholder: 'Select Agent *',
				width: '100%',
			});

            $('#hub_id').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Select Hub *'
			}).bind('select2:select', function() {

                $.ajax({
                        url: '{!! route('admin.debriefing.supervisor.agents') !!}',
                        method: 'POST',
                        data: {
                            'hub_id': $(this).val(),
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {

                            if(data.status){
                                $('#assign_agent_id').empty().append('<option selected="selected" placeholder="Select Hub *" value="">text</option>');
                                $('#agend_input').removeClass('d-none');
                                $.each(data.agents, function (index, agent) {

                                    $('#assign_agent_id').append('<option value="'+agent.id+'" >'+agent.name+'</option>')
                                });
                            }else{
                                $('#agend_input').addClass('d-none');

                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            
                });
				
			});

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.debriefing.supervisor.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Delivery Note No.');
                            head.push('Hub');
                            head.push('Rider');
                            head.push('No. Of Shipments');
                            head.push('No. Of Delivered Shipments');
                            head.push('No. Of Un-Delivered Shipments');
                            head.push('No. Of Pending Shipments');
                            head.push('Target Cash');
                            head.push('Pending Cash Collection');
                            head.push('Assigned Agent');
                            head.push('Fake Status');
                            head.push('Caller agent Call Ratio');
                            head.push('Received delivery Verify Status Call Ratio');
                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.delivery_note_id_padded);
                                row.push(values.hub);
                                row.push(values.rider);
                                row.push(values.shipments_count);
                                row.push(values.delivered_shipments);
                                row.push(values.shipments_undelivered_count);
                                row.push(values.shipments_pending_count);
                                row.push(values.amount);
                                row.push(values.pending_cash_collection);
                                row.push(values.assigned_agent);
                                row.push(values.shipments_fake_status_count);
                                row.push(values.call_agent_ratio);
                                row.push(values.received_verify_delivery_ratio);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Supervisor Dashboard',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                        className: 'btn btn-primary'
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.debriefing.supervisor.list') }}',
                },
                rowId: 'delivery_note_id',
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'delivery_note' ,name: 'delivery_notes.id', class: 'align-middle delivery_note'},
                    { data:'hub' ,name: 'oc.name', class: 'align-middle hub'},
                    { data:'rider' ,name: 'riders.name', class: 'align-middle rider'},
                    { data:'shipments_count_link' ,name: 'delivery_notes.shipments_count', class: 'align-middle shipments_count_link'},
                    { data:'delivered_shipments_link' ,name: 'delivery_notes.delivered_shipments', class: 'align-middle delivered_shipments_link'},
                    { data:'undelivered_shipments_link' ,name: 'undelivered_shipments_link', class: 'align-middle undelivered_shipments_link',orderable: false, searchable: false},
                    { data:'pending_shipments_link' ,name: 'pending_shipments_link', class: 'align-middle pending_shipments_link',orderable: false, searchable: false},
                    { data:'amount' ,name: 'delivery_notes.total_cod_amount', class: 'align-middle amount'},
                    { data:'pending_cash_collection' ,name: 'delivery_notes.received_cod_amount', class: 'align-middle pending_cash_collection'},
                    { data:'assigned_agent' ,name: 'agent.name', class: 'align-middle assigned_agent'},
                    { data:'fake_shipments_link' ,name: 'shipments_fake_status_count', class: 'align-middle fake_shipments_link', orderable: false, searchable: false},
                    { data:'call_agent_ratio' ,name: 'call_agent_ratio', class: 'align-middle call_agent_ratio', orderable: false, searchable: false},
                    { data:'received_verify_delivery_ratio' ,name: 'received_verify_delivery_ratio', class: 'align-middle received_verify_delivery_ratio', orderable: false, searchable: false},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                drawCallback: function (settings) {
                    var api = new $.fn.dataTable.Api( settings );
                    var data = api.rows( {page:'current'} ).data();

                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.undelivered_shipments_link') || $(header).is('.pending_shipments_link') || $(header).is('.fake_shipments_link') || $(header).is('.action') || $(header).is('.received_verify_delivery_ratio') || $(header).is('.call_agent_ratio')) {
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

            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click','tr td.shipments_count_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

                    $.ajax({
                        url: '{!! route('admin.delivery.receive.shipments') !!}',
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
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

                    $.ajax({
                        url: '{!! route('admin.delivery.receive.receive_shipments_delivered') !!}',
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

            $('#datatable tbody').on('click','tr td.undelivered_shipments_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

                    $.ajax({
                        url: '{!! route('admin.delivery.receive.receive_shipments_undelivered') !!}',
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


            $('#datatable tbody').on('click','tr td.pending_shipments_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

                    $.ajax({
                        url: '{!! route('admin.delivery.receive.receive_shipments_pending') !!}',
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


            $('#datatable tbody').on('click','tr td.fake_shipments_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.delivery.receive.fake_status_shipments') !!}',
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

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {

                var id = parseInt($(this).parents('tr').attr('id'));

                if($(this).hasClass('assign_agent')){

                    $('#delivery_note_id_input').val(id);
                    $('#AssignAgentModal').modal('show');

                }

                if($(this).hasClass('bot_sms')){
                    if(id){
                        $.ajax({
                            url: '{!! route('admin.debriefing.supervisor.get_undelivered_shipments') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'delivery_note_id': id
                            }
                        })
                            .done(function(data) {
                                $('#sms_undelivered_shipments').html('');
                                if (data.status == 0) {
                                    var html = '';
                                    html += '<div class="row">';
                                    html += '<div class="col-12">';
                                    html += '<table class="table table-sm table-bordered mb-0" ID="myTable">';

                                    html += '<thead>';
                                    html += '<tr>';
                                    html += '<th><strong>Tracking Number</strong></th>';
                                    html += '<th><strong>Status</strong></th>';
                                    html += '<th><strong>Reason <fieldset class="form-group m-0 position-relative has-icon-right"><input type="text" id="ReasonSearch" class="form-control form-control-sm input-sm primary"><div class="form-control-position primary"><i class="la la-search"></i></div></fieldset></strong></th>';
                                    html += '<th><strong>Number of Attempt(s)</strong></th>';
                                    html += '<th><strong><input type="checkbox" id="selectAll" checked /></strong></th>';
                                    html += '</tr>';
                                    html += '</thead>';
                                    html += '<tbody>';


                                    if (data.shipments_data) {
                                        $.each(data.shipments_data, function(index, shipment) {
                                            $row = '';
                                            $row += '<tr>';
                                            $row += '<td><strong><u><a href='+route+'?tracking_number='+ shipment.tracking_number+' target="_blank">'+shipment.tracking_number+'</a></u></strong></td>';
                                            $row += '<td>'+ shipment.status +'</td>';
                                            $row += '<td>'+ shipment.reason +'</td>';
                                            $row += '<td>'+ shipment.reattempt +'</td>';
                                            $row += '<td><input type="checkbox" name="shipment_ids['+index+']" data-id="'+ index +'" class="form-control sms_checkbox" checked></td>';
                                            $row += '</tr>';
                                            html += $row;
                                        });
                                    }
                                    html += '</tbody></table></div></div>';
                                    $('#sms_delivery_note_id').val(id);
                                    $('#sms_undelivered_shipments').html(html);
                                    $('#shipments_sms_modal').modal('show');
                                    var tbltaha;
                                    $('#ReasonSearch').keyup(function(e){
                                        var input, filter, table, tr, td, i, txtValue;
                                        input = document.getElementById("ReasonSearch");
                                        filter = input.value.toUpperCase();
                                        tbltaha = document.getElementById("myTable");
                                        tr = tbltaha.getElementsByTagName("tr");
                                        for (i = 0; i < tr.length; i++) {
                                            td = tr[i].getElementsByTagName("td")[2];
                                            if (td) {
                                            txtValue = td.textContent || td.innerText;
                                            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                                tr[i].style.display = "";
                                            } else {
                                                tr[i].style.display = "none";
                                            }
                                            }       
                                        }
                                        if($('.sms_checkbox:checked').length == $('.sms_checkbox').filter(':visible').length){
                                            $('#selectAll').prop('checked',true);
                                        }else{
                                            $('#selectAll').prop('checked',false);
                                        }
                                    });
                                    $('#selectAll').click(function(e){
                                    //var tbltaha= $(e.target).closest('table');
                                    $('td input:checkbox',tbltaha).filter(':visible').prop('checked',this.checked);
                                    
                                    });

                                    $('.sms_checkbox').on('click',function(){
                                        if($('.sms_checkbox:checked').length == $('.sms_checkbox').length){
                                            $('#selectAll').prop('checked',true);
                                        }else{
                                            $('#selectAll').prop('checked',false);
                                        }
                                    });
                                }
                                else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            });
                    }
                }


            });

            $('#send_sms_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-control'));
                },
                submitHandler: function(form) {
                    checked_selected_shipments = $("input:checkbox.sms_checkbox:checked").length;

                    $flag = true;
                    if(checked_selected_shipments <= 0){
                        error = "Please select at least one Shipment";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        $flag = false;
                    }
                    if($flag == true){
                        swal({
                            title: 'Are You Sure?',
                            text: 'SMS will be sent to following consignee(s)!',
                            icon: 'warning',
                            buttons: {
                                cancel: {
                                    text: 'No',
                                    value: null,
                                    visible: true,
                                    closeModal: true,
                                },
                                confirm: {
                                    text: 'Yes',
                                    value: true,
                                    visible: true,
                                    closeModal: true
                                }
                            },
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true
                        }).then(function (confirm) {
                            if (confirm) {
                                var shipment_ids = [];
                                $('td input.sms_checkbox').each(function (index, box){
                                    if($(box).is(':checked')){
                                        shipment_ids.push($(box).data('id'));
                                    }
                                });

                                var sms_delivery_note_id = $('#sms_delivery_note_id').val();


                                $.ajax({
                                    url: '{!! route('admin.debriefing.supervisor.send_sms') !!}',
                                    method: 'POST',
                                    data: {
                                        'delivery_note_id': sms_delivery_note_id,
                                        'shipment_ids': shipment_ids,
                                        '_token': '{{ csrf_token() }}'
                                    }
                                })
                                .done(function (data){
                                    if(data.status == 0){
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                    }
                                    else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }
                                    $('#shipments_sms_modal').modal('hide');
                                    table.draw(false);
                                });
                            }
                        });
                    }
                }
            });

            $('#AssignAgentModal').on('hide.bs.modal', function (){
               $('#assign_agent_form #hub_id').val('').trigger('change');
                $('#agend_input').addClass('d-none');
            });

            $('body').on('click','.printdeliverynote',function () {
                var deliverynote = $(this).parents('tr').attr('id');
                print(deliverynote);
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


            $('#assign_agent_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-control'));
                },
                submitHandler: function(form) {


                    swal({
                        title: 'Are You Sure?',
                        text: 'You want to assign this delivery note!',
                        icon: 'warning',
                        buttons: {
                            cancel: {
                                text: 'No',
                                value: null,
                                visible: true,
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Yes',
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    }).then(function (confirm) {
                        if (confirm) {

                            var delivery_note_id = $('#delivery_note_id_input').val();
                            var assign_agent_id = $('#assign_agent_id').val();
                            $.ajax({
                                url: '{!! route('admin.debriefing.supervisor.assign_agents') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'delivery_note_id': delivery_note_id,
                                    'agent_id': assign_agent_id
                                }
                            })
                            .done(function (data){
                                if(data.status == 0){
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                }
                                else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                                $('#AssignAgentModal').modal('hide');
                                table.draw(false);
                            });
                        }
                    });

                }
            });
           
        });
    </script>
@endsection