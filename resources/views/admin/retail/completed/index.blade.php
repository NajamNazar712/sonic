@extends('admin.layout.master')

@section('title', 'Completed Deliveries Retail')

@section('content')

    <h1 class="mb-1">
       Completed Deliveries Retail
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="col">
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

                    <form id="post_pickup_note_ids_form" action="{{route('admin.delivery.completed.retail.deposit.pncc')}}" method="post">
                        @csrf
                        <input type="hidden" name="pickup_note_ids" id="pickup_note_ids">
                    </form>

                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1"></th>
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">RNCC No.</th>
                            <th class="border-primary border-darken-1">Hub</th>
                            <th class="border-primary border-darken-1">Rider</th>
                            <th class="border-primary border-darken-1">Center/Franchise Name</th>
                            <th class="border-primary border-darken-1">No Of Shipments</th>
                            <th class="border-primary border-darken-1">Center and Franchise Code</th>
                            <th class="border-primary border-darken-1">Assigned By</th>
                            <th class="border-primary border-darken-1">Assigned Date</th>
                            <th class="border-primary border-darken-1">Cash Collected By</th>
                            <th class="border-primary border-darken-1">Cash Collection Date</th>
                            <th class="border-primary border-darken-1">RNCC Amount</th>
{{--                            <th class="border-primary border-darken-1">Action</th>--}}
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.delivery.completed.retail.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('RNCC No.');
                            head.push('Hub');
                            head.push('Rider');
                            head.push('Center/Franchise Name');
                            head.push('No. Of Shipments Delivered');
                            head.push('Center and Franchise Code');
                            head.push('Assigned By');
                            head.push('Assigned Date');
                            head.push('Cash Collected By');
                            head.push('Cash Collected Date');
                            head.push('RNCC Amount');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.retail_pickup_note_id);
                                row.push(values.hub);
                                row.push(values.rider);
                                row.push(values.store);
                                row.push(values.shipment_count);
                                row.push(values.code);
                                row.push(values.assignee);
                                row.push(values.assigned_at);
                                row.push(values.collected_by);
                                row.push(values.cash_collected_at);
                                row.push(values.amount);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: false, scrollY: '500px',
                buttons: [
                    {
                        text: 'Deposit RNCC',
                        className: 'btn btn-primary deposit',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to Deposit RNCC!',
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
                                        $('#pickup_note_ids').val(selected_rows);
                                        var pickup_note_ids = $('#pickup_note_ids').val();
                                        // console.log(pickup_note_ids)
                                        if(pickup_note_ids != ''){
                                            $('#post_pickup_note_ids_form').submit();
                                        }
                                    }
                                });


                            }else{
                                var error = "Something went wrong please refresh page and try again!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                        }
                    },
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());

                                    hub_id = $(row.node()).data('hub');

                                    var allow = false;

                                    if(hub_ids.length == 0) {
                                        hub_ids.push(hub_id);

                                        allow = true;
                                    }
                                    else if(hub_ids[0] == hub_id) {
                                        allow = true;
                                    }

                                    if (allow) {
                                        row.select();

                                        var index = $.inArray(id, selected_rows);

                                        if (index === -1) {
                                            selected_rows.push(id);
                                        }

                                        table.button('.deposit').enable();
                                    }
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

                                if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.deposit').disable();

                                        hub_ids.splice(index, 1);
                                    }
                                }
                            });
                        }
                    },{
                        extend: 'excel',
                        title: 'Completed Deliveries Retail',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'],
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
                ajax: {
                    url:'{{ route('admin.delivery.completed.retail.list') }}',
                    data:function (d) {
                        d.search_tracking = $('#search_tracking').val();
                    }
                },
                rowId: 'retail_pickup_note_id',
                order: [[1, 'asc']],
                columns: [
                    {data: 'retail_pickup_note_id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'retail_pickup_note_id' ,name: 'retail_pickup_notes.id', class: 'align-middle text-center retail_pickup_note_id'},
                    { data:'hub' ,name: 'oc.name', class: 'align-middle hub'},
                    { data:'rider' ,name: 'r.name', class: 'align-middle rider'},
                    { data:'store' ,name: 'store', class: 'align-middle store', orderable: false, searchable: false},
                    { data:'count' ,name: 'retail_pickup_notes.shipments', class: 'align-middle count text-center'},
                    { data:'code' ,name: 'code', class: 'align-middle code text-center', orderable: false, searchable: false},
                    { data:'assignee' ,name: 'a.name', class: 'align-middle assignee'},
                    { data:'assigned_at' ,name: 'retail_pickup_notes.assigned_at', class: 'align-middle assigned_at'},
                    { data:'collected_by' ,name: 'cc.name', class: 'align-middle collected_by'},
                    { data:'cash_collected_at' ,name: 'retail_pickup_notes.cash_collected_at', class: 'align-middle cash_collected_at'},
                    { data:'amount' ,name: 'retail_pickup_notes.amount', class: 'align-middle amount'},
                    // { data:'action' ,name: 'action', class: 'align-middle action',orderable: false, searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.delivery_note_id, selected_rows) !== -1) {
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

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action') || $(header).is('.store') || $(header).is('.code')) {
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

            $('#search_tracking').on('change',function () {
                table.draw();
            });
            $('#select_dn').on('change',function () {
                var id = $(this).val();
                row = table.row('#' + id);
                if(row.length >0) {
                    row.select();
                    scan_sound(1);
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
                        table.button('.deposit').enable();
                    }
                    else {
                        table.button('.deposit').disable();
                        hub_ids.splice(index, 1);
                    }
                }else{
                    scan_sound(2);
                    var error = "Pickup Note not found!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                $(this).val('');

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
                        table.button('.deposit').enable();
                    }
                    else {
                        table.button('.deposit').disable();
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
                            table.button('.deposit').enable();
                        }
                        else {
                            hub_ids.splice(index, 1);
                            table.button('.deposit').disable();
                        }
                    }else{
                        var error = "Selected hubs should be the same!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        return false;
                    }
                }
            });

            var route = '{!! route('admin.tracking.index') !!}';
            $('#datatable tbody').on('click','tr td.count button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));

                if(id){

                    $.ajax({
                        url: '{!! route('admin.delivery.completed.retail.shipments.delivered') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'retail_pickup_note_id': id
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
                                $('#delivered_shipments_modal .modal-body').html('');
                                $('#delivered_shipments_modal').modal('show');
                                $('#delivered_shipments_modal .modal-body').html(html);
                            }
                        });
                }

            });

        });
    </script>
@endsection