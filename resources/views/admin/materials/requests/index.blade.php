@extends('admin.layout.master')

@section('title', 'Packaging Material Requests')

@section('content')
    <h1 class="mb-1">
        Packaging Material Requests
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="container justify-content-center pb-2 text-center">
                    <div class="row">
                        <div class="col-3"><h4>Small Flyers: <u id="sm_flyers_title">{{$packaging->small_flyers}}</u></h4></div>
                        <div class="col-3"><h4>Medium Flyers: <u id="md_flyers_title">{{$packaging->medium_flyers}}</u></h4></div>
                        <div class="col-3"><h4>Large Flyers: <u id="lg_flyers_title">{{$packaging->large_flyers}}</u></h4></div>
                        <div class="col-3"><h4>Boxes: <u id="box_title">{{$packaging->boxes}}</u></h4></div>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Requested Date/Time</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">Small Flyers</th>
                        <th class="border-primary border-darken-1">Medium Flyers</th>
                        <th class="border-primary border-darken-1">Large Flyers</th>
                        <th class="border-primary border-darken-1">Boxes</th>
                        <th class="border-primary border-darken-1">Amount</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Payment Mode</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Action</th>
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.packaging.requests.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Shipper');
                            head.push('Requested Date/Time');
                            head.push('City');
                            head.push('Small Flyers');
                            head.push('Medium Flyers');
                            head.push('Large Flyers');
                            head.push('Boxes');
                            head.push('Amount');
                            head.push('Address');
                            head.push('Payment Mode');
                            head.push('Tracking Number');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.shipper);
                                row.push(values.created_at);
                                row.push(values.city);
                                row.push(values.small_flyers);
                                row.push(values.medium_flyers);
                                row.push(values.large_flyers);
                                row.push(values.boxes);
                                row.push(values.amount);
                                row.push(values.address);
                                row.push(values.mode);
                                row.push(values.tracking_number);
                                row.push(values.status);

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
                scrollX: true, scrollY: '350px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Packaging Material Requests',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.packaging.requests.list') }}',
                rowId: 'request_id',
                order: [[2, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'created_at', name: 'packaging_material_requests.created_at', class: 'align-middle created_at'},
                    {data: 'city', name: 'ct.name', class: 'align-middle city'},
                    {data: 'small_flyers', name: 'packaging_material_requests.small_flyers', class: 'align-middle small_flyers'},
                    {data: 'medium_flyers', name: 'packaging_material_requests.medium_flyers', class: 'align-middle medium_flyers'},
                    {data: 'large_flyers', name: 'packaging_material_requests.large_flyers', class: 'align-middle large_flyers'},
                    {data: 'boxes', name: 'packaging_material_requests.boxes', class: 'align-middle boxes'},
                    {data: 'amount', name: 'packaging_material_requests.amount', class: 'align-middle amount'},
                    {data: 'address', name: 'packaging_material_requests.address', class: 'align-middle address'},
                    {data: 'mode', name: 'ppm.id', class: 'align-middle mode'},
                    {data: 'tracking_number_link', name: 'packaging_material_requests.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'status', name: 'packaging_material_requests.status', class: 'align-middle status'},
                    {data: 'action', name: 'action', class: 'align-middle action',orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Booked</option>' +
                        '<option value="1">Dispatched</option>' +
                        '</select>';
                    var payment_mode_select = '<select name="payment_mode_select" id="payment_mode_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.mode')){
                            $(payment_mode_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data1 = $.map({!! $payment_mode !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data1 = $.map({!! $payment_mode !!}, function (obj) {
                        obj.text = obj.mode;

                        return obj;
                    });

                    $("#payment_mode_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Mode",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
            //dispatch
            $('body').on('click','.dispatch',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Dispatch Packaging Material!',
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
                            url: '{!! route('admin.packaging.requests.dispatch') !!}',
                            method: 'POST',
                            data: {
                                'id': request_id,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {

                            if(data.status === 1){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                setTimeout(function(){
                                    window.location.reload();
                                },2000);
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                        });
                    }
                });

            });

        });

    </script>
@endsection