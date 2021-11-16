@extends('admin.layout.master')

@section('title', 'Bags In Transit ')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Bags In Transit
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="text-center">
                                <form id="manifest_number_search_form" class="d-inline-block form-inline mb-1 justify-content-center text-left" novalidate="novalidate">
                                    <div class="form-group mr-1">
                                        <input type="text" name="manifest_number" class="form-control manifest_number" id="manifest_number" placeholder="Manifest Number">
                                    </div>
                                </form>

                                <form id="vehicle_number_search_form" class="d-inline-block form-inline mb-1 justify-content-center text-left" novalidate="novalidate">
                                    <div class="form-group">
                                        <input type="text" name="vehicle_number" class="form-control vehicle_number" id="vehicle_number" placeholder="Vehicle Number">
                                    </div>
                                </form>

                                <form id="tracking_number_search_form" class="d-inline-block form-inline ml-1 mb-1 justify-content-center" novalidate="novalidate">
                                    <div class="form-group">
                                        <input type="text" name="tracking_number" class="form-control tracking_number" id="tracking_number" placeholder="Tracking Number">
                                    </div>
                                </form>

                                <form id="bag_number_search_form" class="d-inline-block form-inline ml-1 mb-1 justify-content-center" novalidate="novalidate">
                                    <div class="form-group">
                                        <input type="text" name="bag_number" class="form-control bag_number" id="bag_number" placeholder="Bag Number">
                                    </div>
                                </form>
                            </div>


                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Bag Number</th>
                                    <th class="border-primary border-darken-1">Bag Type</th>
                                    <th class="border-primary border-darken-1">Transited Shipments</th>
                                    <th class="border-primary border-darken-1">Short Received Shipments</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Actual Weight</th>
                                    <th class="border-primary border-darken-1">Shipping Mode</th>
                                    <th class="border-primary border-darken-1">Manifest Id</th>
                                    <th class="border-primary border-darken-1">Junctions</th>
                                     <th class="border-primary border-darken-1">Vehicle</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Status Updated At</th>
                                    <th class="border-primary border-darken-1">Status Updated By</th>
                                    <th class="border-primary border-darken-1">Status Hub</th>
                                    <th class="border-primary border-darken-1">Manifest Created At</th>
                                    <th class="border-primary border-darken-1">Transited Date</th>
                                    <th class="border-primary border-darken-1">Transited By</th>
                                    <th class="border-primary border-darken-1">Seal Updated By</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   </div>
    <div class="modal fade" id="SealNumberUpdateModal" data-backdrop="static" role="dialog" aria-labelledby="SealNumberUpdateModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="delivered_shipments_modal_title">Update Seal Number</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="edit_seal_number_form" class="form mb-1 justify-content-center" novalidate="novalidate">
                        <input type="hidden" name="bag_id" id="bag_id" value="">
                        <div class="row justify-content-center">
                            <div class="col-4 form-group">
                                <input type="text" name="seal_number" class="form-control rounded-right seal_number" placeholder="Seal Number*" data-rule-required="true" data-msg-required="Seal Number is required" data-msg-remote="Seal Number must be unique" id="seal_number">
                            </div>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="track" class="btn btn-primary" value="Track">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="junctions" role="dialog" aria-labelledby="junctions" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="vehicle_info" role="dialog" aria-labelledby="vehicle_info" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="shipments" role="dialog" aria-labelledby="shipments_title" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="short_received_shipments" role="dialog" aria-labelledby="short_received_shipments" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style>
        .red{
            background-color: #FFC0CB;
        }
        .green{
            background-color: #8AEFA8;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $.validator.addMethod(
                "seal_number",
                function(value,element){
                    if(element.value.length == 6 || element.value.length == 11 ||  element.value.length == 12 ||  element.value.length == 13){
                        return true;
                    } else {
                        return false;
                    }
                },
                "Invalid Seal Number"
            );

            $('#edit_seal_number_form form input.seal_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#edit_seal_number_form form input.seal_number').on('change', function () {
                var seal = this.value;
                if (seal.length != 12 && seal.length != 13 && seal.length != 6) {
                    this.value = '';
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.cargo_manifest.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Seal Number');
                            head.push('Bag Type');
                            head.push('Shipments');
                            head.push('Short Received');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Actual Weight');
                            head.push('Shipping Mode');
                            head.push('Manifest Id');
                            head.push('Status');
                            head.push('Status Updated At');
                            head.push('Status Updated By');
                            head.push('Status Hub');
                            head.push('Manifest Created At');
                            head.push('Transited By');
                            head.push('Seal Updated By');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.seal_number);
                                row.push(values.bag_type);
                                row.push(values.shipments);
                                row.push(values.short_shipments);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.actual_weight);
                                row.push(values.shipping_mode);
                                row.push(values.manifest);
                                row.push(values.status);
                                row.push(values.status_updated_at);
                                row.push(values.status_updated_by);
                                row.push(values.status_location);
                                row.push(values.manifest_created_at);
                                row.push(values.transitted_by);
                                row.push(values.updated_by);


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
                        title: 'Manifest list',
                        className:'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
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
                    url: '{{ route('admin.cargo_manifest.list') }}',
                    data: function (d) {
                        d.manifest_number = $('#manifest_number_search_form #manifest_number').val();
                        d.vehicle_number = $('#vehicle_number_search_form #vehicle_number').val();
                        d.tracking_number = $('#tracking_number_search_form #tracking_number').val();
                        d.bag_number = $('#bag_number_search_form #bag_number').val();

                    }
                },
                rowId: 'id',
                order: [[16, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'pickup_notes.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'seal_number', name: 'seal_number', class: 'align-middle seal_number'},
                    {data: 'bag_type', name: 'cargo_manifest_bags.type', class: 'align-middle cargo_manifest_bags.type'},
                    {data: 'bag_shipments', name: 'bag_shipments', class: 'align-middle bag_shipments'},
                    {data: 'short_received_shipments', name: 'short_received_shipments', class: 'align-middle short_received_shipments'},
                    {data: 'origin', name: 'oh.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dh.name', class: 'align-middle destination'},
                    {data: 'actual_weight', name: 'actual_weight', class: 'align-middle actual_weight'},
                    {data: 'shipping_mode', name: 'sm.id', class: 'align-middle shipping_mode'},
                    {data: 'manifest_id', name: 'cm.id', class: 'align-middle manifest_id'},
                    {data: 'junctions', name: 'junctions', class: 'align-middle junctions'},
                    {data: 'vehicles', name: 'vehicles', class: 'align-middle vehicles', searchable: false, orderable: false},
                    /*{data: 'arrival_at', name: 'shipments_journey.created_at', class: 'align-middle arrival_at'},*/
                    {data: 'status', name: 'bs.id', class: 'align-middle status'},
                    {data: 'status_updated_at', name: 'cmbj.created_at', class: 'align-middle status_updated_at'},
                    {data: 'status_updated_by', name: 'status_editor.name', class: 'align-middle status_updated_by'},
                    {data: 'status_location', name: 'sedh.name', class: 'align-middle status_location'},
                    {data: 'manifest_created_at', name: 'cm.created_at', class: 'align-middle manifest_created_at'},
                    {data: 'transitted_date', name: 'cargo_manifest_bags.created_at', class: 'align-middle transitted_date'},
                    {data: 'transitted_by', name: 'a.name', class: 'align-middle transitted_by'},
                    {data: 'updated_by', name: 'ah.name', class: 'align-middle updated_by'},
                    {data: 'action', name: 'action', class: 'align-middle action', searchable: false, orderable: false},
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
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control">' +
                        '</select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control">' +
                        '</select>';
                    var select_status = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.service_type')){
                            $(service_drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.shipping_mode')){
                            $(mode_drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.status')){
                            $(select_status).appendTo($(search))
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
                    var data2 = $.map({!! $shipping_mode !!}, function (obj) {
                        obj.id = obj.id // replace pk with your identifier

                        return obj;
                    });
                    var data2 = $.map({!! $shipping_mode !!}, function (obj) {
                        obj.text = obj.mode; // replace name with the property used for the text

                        return obj;
                    });

                    $("#mode_select").prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Mode",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data3 = $.map({!! $bag_status !!}, function (obj) {
                        obj.id = obj.id // replace pk with your identifier

                        return obj;
                    });
                    var data3 = $.map({!! $bag_status !!}, function (obj) {
                        obj.text = obj.name; // replace name with the property used for the text

                        return obj;
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data:data3,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('body').on('click','button.edit_seal_number',function () {
                var id = $(this).parents('tr').attr('id');
                var current_seal_number = parseInt(table.row($(this).parents('tr')).data().seal_number);
                console.log(id,current_seal_number);
                $('#bag_id').val(id);
                $('#edit_seal_number').val(current_seal_number);
                $('#SealNumberUpdateModal').modal('show');
            });



            $('#edit_seal_number_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form) {
                    var seal_number = $('#seal_number').val();
                    var bag_id = $('#bag_id').val();
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to update seal number!',
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
                        if(confirm){
                            if(bag_id && seal_number){
                                $.ajax({
                                    url: '{!! route('admin.cargo_manifest.update.seal_number') !!}',
                                    method: 'POST',
                                    data: {
                                        'id':bag_id,
                                        'seal_number':seal_number,
                                        '_token': '{{ csrf_token() }}'
                                    }
                                }).done(function (data) {
                                    if(data.status === 1){
                                        table.draw();
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                        $('#SealNumberUpdateModal').modal('hide');
                                    }else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }

                                });
                            }
                        }
                    });
                }
            });

            $('#datatable tbody').on('click', 'tr td.junctions button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                $('#junctions .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.cargo_manifest.junctions_info') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        if (data.status == 1) {

                            var head = '';
                            var junctions = '';
                            var date = '';

                            head = '<h4 class="modal-title" id="shipments_title">Junction(s)</h4>' +
                                '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                                '<span aria-hidden="true">×</span>\n' +
                                '</button>';

                            $.each(data.junction_name, function(index,value) {
                                junctions += value + ',' ;
                            });

                            if(data.received_date !== ""){
                                date = 'Received Date' + data.received_date;
                                junctions += date;
                            }
                            $('#junctions .modal-header').html(head);
                            $('#junctions .modal-body').html(junctions);

                            $('#junctions').modal('show');
                        }
                    });
            });

            $('#datatable tbody').on('click', 'tr td.vehicles button', function() {

                var manifest_id = table.row($(this).parents('tr')).data().manifest;

                if(manifest_id){
                    $('#vehicle_info .modal-body').html('');

                    $.ajax({
                        url: '{!! route('admin.cargo_manifest.vehicle_info') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'manifest_id': manifest_id
                        }
                    })
                        .done(function(data) {

                                var head = '';
                                var vehicle = '';
                                var date = '';

                                head = '<h4 class="modal-title" id="shipments_title">Vehicle Info</h4>' +
                                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                                    '<span aria-hidden="true">×</span>\n' +
                                    '</button>';

                            vehicle +='<table class="table table-bordered"><thead><tr><th>Name</th>\n' +
                                '      <th>Phone Number</th>\n' +
                                '      <th>Vehicle Number</th></tr></thead><tbody><tr><td>'+data.driver_name +'</td><td>'+data.driver_phone_no +'</td><td>'+data.vehicle_number +'</td></tr></tbody></table>';

                                $('#junctions .modal-header').html(head);
                                $('#junctions .modal-body').html(vehicle);

                                $('#junctions').modal('show');

                        });
                }

            });
            var route = '{!! route('admin.tracking.index') !!}';
            $('#datatable tbody').on('click', 'tr td.bag_shipments button', function() {
                var seal_number = table.row($(this).parents('tr')).data().seal_number;

                $('#shipments .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.cargo_manifest.transitted_shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'seal_number': seal_number
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var head = '';
                            var tracking_numbers = '';

                            head = '<h4 class="modal-title" id="shipments_title">Shipment(s)</h4>' +
                                '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                                '<span aria-hidden="true">×</span>\n' +
                                '</button>';

                            $.each(data, function(index, tracking_number) {
                                tracking_numbers += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                            });

                            $('#shipments .modal-header').html(head);
                            $('#shipments .modal-body').html(tracking_numbers);

                            $('#shipments').modal('show');
                        }
                    });
            });

            $('#datatable tbody').on('click', 'tr td.short_received_shipments button', function() {
                var seal_number = table.row($(this).parents('tr')).data().seal_number;

                $('#short_received_shipments .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.cargo_manifest.short_received_shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'seal_number': seal_number
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var head = '';
                            var tracking_numbers = '';

                            head = '<h4 class="modal-title" id="shipments_title">Short Received Shipment(s)</h4>' +
                                '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                                '<span aria-hidden="true">×</span>\n' +
                                '</button>';

                            $.each(data, function(index, tracking_number) {
                                tracking_numbers += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                            });

                            $('#short_received_shipments .modal-header').html(head);
                            $('#short_received_shipments .modal-body').html(tracking_numbers);

                            $('#short_received_shipments').modal('show');
                        }
                    });
            });

            $('#datatable tbody').on('click', '.manifest_id', function () {
                var manifest_id = table.row($(this).parents('tr')).data().manifest;
              
                $.ajax({
                    url: '{!! route('admin.cargo_manifest.print') !!}',
                    method: 'POST',
                    data: {
                        'cargo_manifest_ids': manifest_id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function (data) {
                        var tab = window.open('', '_blank');

                        if (!tab) {
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
            });

            $('#bag_number_search_form').bind('submit', function(e) {
                e.preventDefault();

                table.draw();
            });
            $('#manifest_number_search_form').bind('submit', function(e) {
                e.preventDefault();

                table.draw();
            });
            $('#vehicle_number_search_form').bind('submit', function(e) {
                e.preventDefault();

                table.draw();
            });

            $('#tracking_number_search_form').bind('submit', function(e) {
                e.preventDefault();

                length = $('#tracking_number_search_form #tracking_number').val().length;

                if (length == 0 || length >= 12) {
                    table.draw();
                }
            });
        });
    </script>
@endsection