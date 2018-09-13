@extends('admin.layout.master')
@section('title','Completed Deliveries')
@section('content')
    <h1 class="mb-1">
        Completed Deliveries
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')



                <div class="row mb-2 justify-content-center">

                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" class="form-control" placeholder="Search By Tracking Number" id="search_tracking">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" class="form-control" placeholder="Scan To Select" id="select_dn">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                        </fieldset>
                    </div>


                </div>
                <form id="post_delivery_note_ids_form" action="{{route('admin.delivery.completed.deposit.dncc')}}" method="post">
                    @csrf
                    <input type="hidden" name="delivery_note_ids" id="delivery_note_ids">
                </form>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1"></th>
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
                        <th class="border-primary border-darken-1">DNCC Amount</th>
                    </tr>
                    </thead>
                </table>

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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                @if (session('role_id') == 1 || in_array(41, session('permissions')))
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    buttons: [{
                        text: 'Deposit DNCC',
                        className: 'btn btn-primary delivered',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to Deposit DNCC!',
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
                                        $('#delivery_note_ids').val(selected_rows);
                                        var delivery_note_ids = $('#delivery_note_ids').val();
                                        // console.log(delivery_note_ids)
                                        if(delivery_note_ids != ''){
                                            $('#post_delivery_note_ids_form').submit();
                                        }
                                    }
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
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                scrollX: true, scrollY: '300px',
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url:'{{ route('admin.delivery.completed.list') }}',
                    data:function (d) {
                        d.search_tracking = $('#search_tracking').val();
                    }
                },
                rowId: 'delivery_note_id',
                order: [[2, 'asc']],
                columns: [
                    {data: 'delivery_note_id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'delivery_note' ,name: 'delivery_notes.id', class: 'align-middle text-center delivery_note'},
                    { data:'hub' ,name: 'oc.name', class: 'align-middle hub'},
                    { data:'rider' ,name: 'riders.name', class: 'align-middle rider'},
                    { data:'route' ,name: 'route', class: 'align-middle route'},
                    { data:'shipments_count' ,name: 'shipments_count', class: 'align-middle shipments_count'},
                    { data:'delivered_shipments' ,name: 'delivered_shipments', class: 'align-middle delivered_shipments'},
                    { data:'assignee' ,name: 'admins.name', class: 'align-middle assignee'},
                    { data:'created_at' ,name: 'created_at', class: 'align-middle created_at'},
                    { data:'updated_by' ,name: 'ub.name', class: 'align-middle updated_by'},
                    { data:'updated_at' ,name: 'delivery_notes.updated_at', class: 'align-middle updated_at'},
                    { data:'amount' ,name: 'delivery_notes.received_cod_amount', class: 'align-middle amount'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number')) {
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
            var hub_ids = [];
            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {

                var id = parseInt($(this).parent('tr').attr('id'));
                var hub_id = $(this).parents('tr').data('hub');
                if(hub_ids.length == 0){
                    hub_ids.push(hub_id);
                    var index = $.inArray(id, selected_rows);

                    if (index === -1) {
                        selected_rows.push(id);
                    }
                    else {
                        selected_rows.splice(index, 1);
                    }

                    if (selected_rows.length > 0) {
                        table.button('.delivered').enable();
                    }
                    else {
                        table.button('.delivered').disable();
                        hub_ids.splice(index, 1);
                    }
                }else{
                    if(hub_ids[0] == hub_id){
                        var index = $.inArray(id, selected_rows);

                        if (index === -1) {
                            selected_rows.push(id);
                        }
                        else {
                            selected_rows.splice(index, 1);
                        }

                        if (selected_rows.length > 0) {
                            table.button('.delivered').enable();
                        }
                        else {
                            hub_ids.splice(index, 1);
                            table.button('.delivered').disable();
                        }
                    }else{
                        var error = "Selected hubs should be the same!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        return false;
                    }

                }

            });

            {{--$('#search_tracking').on('change',function () {--}}
                {{--var input = $(this);--}}
                {{--var tracking = $(this).val();--}}
                {{--var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;--}}

                {{--if(numberRegex.test(tracking)) {--}}
                    {{--$.ajax({--}}
                        {{--url:'{{route('admin.delivery.receive.tracking.search')}}',--}}
                        {{--type:'GET',--}}
                        {{--dataType:'JSON',--}}
                        {{--data: {--}}
                            {{--'tracking':tracking--}}
                        {{--}--}}
                    {{--}).done(function(data){--}}
                        {{--if(data.status == 0){--}}

                            {{--table--}}
                                {{--.columns( 1 )--}}
                                {{--.search( data.delivery_note )--}}
                                {{--.draw();--}}
                            {{--// input.val('');--}}
                        {{--}else{--}}
                            {{--table--}}
                                {{--.columns( 1 )--}}
                                {{--.search( 0 )--}}
                                {{--.draw();--}}
                            {{--toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}

                        {{--}--}}

                    {{--});--}}
                {{--}else{--}}
                    {{--table--}}
                        {{--.columns( 1 )--}}
                        {{--.search( 0 )--}}
                        {{--.draw();--}}
                    {{--input.val('');--}}
                {{--}--}}
            {{--});--}}

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
            $('body').on('click','.printdeliverynote',function () {
                var deliverynote = $(this).parents('tr').attr('id');
                // console.log(deliverynote);
                print(deliverynote);
            });
            function printDNCC(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.dncc.print') !!}',
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

            $('body').on('click','.printDNCC',function () {
                var note_id = $(this).parents('tr').attr('id');
                printDNCC(note_id);
            });

            {{--$('#scan_tracking').on('change',function () {--}}
                {{--var scan = $(this);--}}
                {{--var tracking = $(this).val();--}}
                {{--var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;--}}
                {{--if(numberRegex.test(tracking)) {--}}
                    {{--var url = "{{route("admin.delivery.receive.status","id")}}";--}}
                    {{--url = url.replace('id',tracking);--}}
                    {{--// console.log(url);--}}
                    {{--window.location.href = url;--}}
                {{--}else{--}}
                    {{--scan.val('');--}}
                {{--}--}}
            {{--});--}}
            $('#search_tracking').on('change',function () {
                table.draw();
            });
            $('#select_dn').on('change',function () {
                var id = $(this).val();
                row = table.row('#' + id);
                if(row.length >0) {
                    row.select();

                    if (hub_ids.length == 0) {
                        hub_ids.push(row.data().hub_id);
                    }
                    var index = $.inArray(id, selected_rows);

                    if (index === -1) {
                        selected_rows.push(id);
                    }
                else {
                        row.deselect();
                        selected_rows.splice(index, 1);
                    }

                    if (selected_rows.length > 0) {
                        table.button('.delivered').enable();
                    }
                    else {
                        table.button('.delivered').disable();
                        hub_ids.splice(index, 1);
                    }
                }else{
                    var error = "Delivery Note not found!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                }
                $(this).val('');

            });

        });
    </script>
@endsection