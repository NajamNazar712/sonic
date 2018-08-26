@extends('admin.layout.master')

@section('content')
    <h1 class="mb-1">
        Receive Return Deliveries(Return Note: {{$return_note_id}})
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
                            <th class="border-primary border-darken-1">Address</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Service Type</th>
                            <th class="border-primary border-darken-1">Action</th>
                        </tr>
                        </thead>
                    </table>
                    <div class="row justify-content-center">
                        <div class="col-2">
                            <button id="statusSubmit" type="submit" disabled class="btn btn-primary btn-block">Update Status</button>
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
                            if(selected_rows != ''){
                                $.ajax({
                                    url: '{!! route('admin.return.receive.status.delivered') !!}',
                                    method: 'POST',
                                    data: {
                                        'shipment_ids': selected_rows,
                                        'return_note_id': note_id,
                                        '_token': '{{ csrf_token() }}'
                                    }
                                }).done(function (data) {
                                    if(data.status == 0){

                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                    }else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                    }
                                    location.reload();
                                    // $.each(selected_rows, function(index, id) {
                                    //     table.row($('#datatable tbody tr#' + id)).deselect();
                                    // });
                                    // checkShipmentStatuses();
                                    // selected_rows = [];
                                    // table.button('.returned').disable();
                                    // table.ajax.reload();
                                    // $('.reasonDrop','.statusDrop').select2('destroy');
                                    // setTimeout(function () {
                                    //     $(".reasonDrop").select2({
                                    //         placeholder: "Select a Reason",
                                    //         width:'100%'
                                    //     });
                                    //     $(".statusDrop").select2({
                                    //         placeholder: "Select a Status",
                                    //         width:'100%'
                                    //     });
                                    // },2000);

                                });
                            }else{
                                var error = "Something went wrong please refresh page and try again!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                            }
                        }

                    }],
                @else
                    dom: 'ltipr',
                @endif
                fixedHeader: {
                    header: true,
                    headerOffset: $('.header-navbar').height()
                },
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                stateSave: true,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.return.receive.status.list',['id'=>$return_note_id]) }}',
                rowId: 'shId',
                order: [[2, 'asc']],
                columns: [
                    {data: 'shId', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data:'tracking_number',name: 'tracking_number', class: 'align-middle tracking_number'},
                    {data:'shipper',name: 'shipper', class: 'align-middle shipper'},
                    {data:'status',name: 'status', class: 'align-middle status statusOnChange'},
                    {data:'reason',name: 'reason', class: 'align-middle reason reasonSelect'},
                    {data:'remarks',name: 'remarks', class: 'align-middle remarks'},
                    {data:'address',name: 'address', class: 'align-middle address'},
                    {data:'destination',name: 'destination', class: 'align-middle destination'},
                    {data:'service_type',name: 'service_type', class: 'align-middle service_type'},
                    {data:'action',name: 'action', class: 'align-middle action'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function() {
                    $(".reasonDrop").select2({
                        placeholder: "Select a Reason",
                        width:'100%'
                    });
                    $(".statusDrop").select2({
                        placeholder: "Select a Status",
                        width:'100%'
                    });
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.status') || $(header).is('.reason') || $(header).is('.remarks') || $(header).is('.action')) {
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
                    if(data.status == 0){
                        $.each(data.reasons,function (key,value) {
                            var newOption = new Option(value.name, value.id, false, false);
                            reason.append(newOption).trigger('change');
                        });
                    }else{
                        $('.reasonDrop').empty();
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
            var shipments = [];
            $('#status_update_form').bind('submit', function(event) {
                var shipment = $('#shipment_ids');
                event.preventDefault();
                var id = '';
                var count = table.data().count();
                for(var i = 0;i<count;i++){
                    id = table.row( i ).id();
                    shipments.push(id);
                }
                shipment.val(shipments);
                this.submit();
            });



        });
    </script>
@endsection