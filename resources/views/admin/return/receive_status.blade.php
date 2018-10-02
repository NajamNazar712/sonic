@extends('admin.layout.master')
@section('title','Receive Return Deliveries')

@section('content')
    <h1 class="mb-1">
        Return Receive Deliveries(Return Note: {{str_pad($return_note_id, 6, '0', STR_PAD_LEFT)}})
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="status_update_form" action="{{route('admin.return.receive.status.submit')}}" method="post">
                    @csrf
                    <input type="hidden" value="{{$return_note_id}}" id="return_note" name="return_note_id">
                    <input type="hidden" value="{{$shipments_count}}" id="shipments_count" name="shipments_count">
                    <input type="hidden" name="shipment_ids" id="shipment_ids">
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1"></th>
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">Shipper</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Reason</th>
                            <th class="border-primary border-darken-1">Remarks</th>
                            <th class="border-primary border-darken-1">Current Status</th>
                            <th class="border-primary border-darken-1">Address</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Service Type</th>
                            <th class="border-primary border-darken-1">Action</th>
                        </tr>
                        </thead>
                    </table>
                    <div class="row justify-content-center">
                        <div class="col-2">
                            <button id="statusSubmit" type="submit" disabled class="btn btn-primary btn-block">Verify Status</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>






@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

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
            border-color: #666EE8;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        table.dataTable tbody tr td.status,
        table.dataTable tbody tr td.reason,
        table.dataTable tbody tr td.remarks {
            min-width: 110px !important;
            max-width: 150px !important;
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    {{-- <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.decimal').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 3,
                'min': 0.00,
                'max': 1000
            });

            var selected_rows = [];
            var note_id = $('#return_note').val();
            var table = $('#datatable').DataTable({
                @if (session('role_id') == 1 || in_array(50, session('permissions')))
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    buttons: [{
                        text: 'Returned',
                        className: 'btn btn-primary returned',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows !== ''){
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to change this shipment\'s status!',
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
                                        $.ajax({
                                            url: '{!! route('admin.return.receive.status.delivered') !!}',
                                            method: 'POST',
                                            data: {
                                                'shipment_ids': selected_rows,
                                                'return_note_id': note_id,
                                                '_token': '{{ csrf_token() }}'
                                            }
                                        }).done(function (data) {
                                            if(data.status === 0){

                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                            }else{
                                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                            }
                                            location.reload();

                                        });
                                    }
                                });

                            }else{
                                var error = "Something went wrong please refresh page and try again!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                        }
                    }, {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.returned').enable();
                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                              var row = table.row(index);

                              if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                row.deselect();

                                id = parseInt(row.id());

                                var index = $.inArray(id, selected_rows);

                                if (index !== -1) {
                                    selected_rows.splice(index, 1);
                                }

                                if (selected_rows.length == 0) {
                                    table.button('.returned').disable();
                                }
                              }
                            });
                        }
                    }],
                @else
                    dom: 'ltipr',
                @endif
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                scrollX: true, scrollY: '350px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: false,
                ajax: '{{ route('admin.return.receive.status.list',['id'=>$return_note_id]) }}',
                rowId: 'shId',
                order: [[3, 'asc']],
                columns: [
                    {data: 'shId', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data:'tracking_number',name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data:'shipper',name: 'users.name', class: 'align-middle shipper'},
                    {data:'status',name: 'status', class: 'align-middle status statusOnChange',orderable: false, searchable: false},
                    {data:'reason',name: 'reason', class: 'align-middle reason reasonSelect',orderable: false, searchable: false},
                    {data:'remarks',name: 'remarks', class: 'align-middle remarks',orderable: false, searchable: false},
                    {data:'current_status_name',name: 'current_status_name', class: 'align-middle current_status_name',orderable: false, searchable: false},
                    {data:'address',name: 'usi.pickup_address', class: 'align-middle address'},
                    {data:'destination',name: 'oc.name', class: 'align-middle destination'},
                    {data:'service_type',name: 'bt.booking_type', class: 'align-middle service_type'},
                    {data:'action',name: 'action', class: 'align-middle action',orderable: false, searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                    if ((data.shipper_status_id != 25) && (data.shipper_status_id != 31) && (data.shipper_status_id != 38)) {
                        $('td:eq(0)', row).addClass('select-checkbox');

                        if ($.inArray(data.shId, selected_rows) !== -1) {
                            table.row(row).select();
                        }
                    }
                },
                drawCallback: function (settings) {
                    $(".reasonDrop").prepend('<option value="" ></option>').select2({
                        placeholder: "Select a Reason",
                        width:'100%'
                    });
                    $(".statusDrop").prepend('<option value="" ></option>').select2({
                        placeholder: "Select a Status",
                        width:'100%'
                    });
                },
                initComplete: function() {

                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.status') || $(header).is('.reason') || $(header).is('.remarks') || $(header).is('.action')|| $(header).is('.current_status_name')) {
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change keypress', function() {
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

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.returned').enable();
                    // table.button(1).enable();
                }
                else {
                    table.button('.returned').disable();
                    // table.button(1).disable();
                }
            });

            $('body').on('select2:select','.statusOnChange .statusDrop',function (e) {
                $('#statusSubmit').removeAttr('disabled');
                var statusSelection = $(this).find(':selected');
                var status = statusSelection.val();
                var reason = statusSelection.closest('td').next('td').find('.reasonDrop');

                $.ajax({
                    url:'{!! route('admin.return.receive.reason') !!}',
                    type:'POST',
                    dataType:'json',
                    data: {
                        'status':status,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 0){
                        $.each(data.reasons,function (key,value) {
                            var newOption = new Option(value.name, value.id, false, false);
                            reason.append(newOption).trigger('change');
                        });
                    }else{
                        reason.empty().trigger('change');
                        toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });
            });
            $('body').on('click','.clear',function () {
                // console.log();
                var status = $(this).parents().closest('tr').find('.statusDrop');
                var reason = $(this).parents().closest('tr').find('.reasonDrop');
                status.val('').trigger("change");
                reason.val('').trigger("change");
                $('.remarks input').val('');
                // $('.reasonDrop').val('').trigger("change");
            });
            $('#status_update_form').on('keypress',function (e) {
                if(e.which == 13) {
                    e.preventDefault();
                }
            });
            var shipments = [];
            $('#status_update_form').bind('submit', function(event) {
                var shipment = $('#shipment_ids');
                event.preventDefault();
                var this_form = this;
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to change shipment\'s status!',
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
                        var id = '';
                        var count = table.data().count();
                        for(var i = 0;i<count;i++){
                            id = table.row( i ).id();
                            shipments.push(id);
                        }
                        shipment.val(shipments);
                        this_form.submit();
                    }
                });

            });



        });
    </script>
@endsection