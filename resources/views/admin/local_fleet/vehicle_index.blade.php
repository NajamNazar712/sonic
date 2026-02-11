@extends('admin.layout.master')

@section('title', 'Local Fleet Vehicles')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row"></div>

            <div class="content-body">
                <h1 class="mb-1">Local Fleet Vehicles</h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Vehicle No</th>
                                    <th class="border-primary border-darken-1">Date</th>
                                    <th class="border-primary border-darken-1">Vehicle Type</th>
                                    <th class="border-primary border-darken-1">Make</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Vendor</th>
                                    <th class="border-primary border-darken-1">Vendor Type</th>
                                    <th class="border-primary border-darken-1">Driver</th>
                                    <th class="border-primary border-darken-1">Capacity</th>
                                    <th class="border-primary border-darken-1">Mileage</th>
                                    <th class="border-primary border-darken-1">Fuel Responsibility</th>
                                    <th class="border-primary border-darken-1">Rent Amount</th>
                                    <th class="border-primary border-darken-1">Rent Type</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                </tr>
                                </thead>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade text-left" id="AddVehicleModal" data-backdrop="static" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-primary white">
                        <h4 class="modal-title">Add Vehicle</h4>
                    </div>

                    <form method="POST" action="{{ route('admin.cargo.supply_chain.local_fleet.vehicle.store') }}" id="add_vehicle_form">
                        @csrf

                        <div class="modal-body">
                            <div class="row">

                                <div class="col-md-6">
                                    <label>Vehicle Number</label>
                                    <input type="text" name="vehicle_number" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label>Make</label>
                                    <input type="text" name="make" class="form-control">
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>City</label>
                                    <select name="city_id" id="city_id" class="form-control select2">
                                        <option value="">Select</option>
                                        @foreach($cities as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Vendor Name</label>
                                    <input type="text" name="vendor_name" class="form-control">
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Vendor Type</label>
                                    <select name="vendor_type" class="form-control">
                                        <option value="1">Vendor</option>
                                        <option value="2">Self</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Driver Name</label>
                                    <input type="text" name="driver_name" class="form-control">
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Capacity</label>
                                    <input type="number" name="capacity" class="form-control">
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Mileage Per Liter</label>
                                    <input type="text" name="mileage_per_liter" class="form-control">
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Vehicle Type</label>
                                    <select name="vehicle_type" class="form-control">
                                        <option value="1">Permanent</option>
                                        <option value="2">Temporary</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Rent Type</label>
                                    <select name="rent_type" class="form-control">
                                        <option value="1">Daily</option>
                                        <option value="2">Monthly</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Rent Amount</label>
                                    <input type="number" name="rent_amount" class="form-control">
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Fuel Responsibility</label>
                                    <select name="fueling_responsibility" class="form-control">
                                        <option value="1">Trax</option>
                                        <option value="2">Vendor</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade text-left" id="EditVehicleModal" data-backdrop="static" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-primary white">
                        <h4 class="modal-title">Edit Vehicle</h4>
                    </div>

                    <form method="POST" action="{{ route('admin.cargo.supply_chain.local_fleet.vehicle.update') }}" id="edit_vehicle_form">
                        @csrf
                        <input type="hidden" name="id" id="edit_id">

                        <div class="modal-body">
                            <div class="row">

                                <div class="col-md-6">
                                    <label>Vehicle Number</label>
                                    <input type="text" id="edit_vehicle_number" name="vehicle_number" class="form-control" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label>Make</label>
                                    <input type="text" id="edit_make" name="make" class="form-control">
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>City</label>
                                    <select id="edit_city_id" name="city_id" class="form-control select2" disabled>
                                        @foreach($cities as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Vendor Name</label>
                                    <input type="text" id="edit_vendor_name" name="vendor_name" class="form-control">
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Vendor Type</label>
                                    <select id="edit_vendor_type" name="vendor_type" class="form-control">
                                        <option value="1">Vendor</option>
                                        <option value="2">Self</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Driver Name</label>
                                    <input type="text" id="edit_driver_name" name="driver_name" class="form-control">
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Capacity</label>
                                    <input type="number" id="edit_capacity" name="capacity" class="form-control">
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Mileage</label>
                                    <input type="text" id="edit_mileage_per_liter" name="mileage_per_liter" class="form-control">
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Vehicle Type</label>
                                    <select id="edit_vehicle_type" name="vehicle_type" class="form-control">
                                        <option value="1">Permanent</option>
                                        <option value="2">Temporary</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Rent Type</label>
                                    <select id="edit_rent_type" name="rent_type" class="form-control">
                                        <option value="1">Daily</option>
                                        <option value="2">Monthly</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Rent Amount</label>
                                    <input type="number" id="edit_rent_amount" name="rent_amount" class="form-control">
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label>Fuel Responsibility</label>
                                    <select id="edit_fueling_responsibility" name="fueling_responsibility" class="form-control">
                                        <option value="1">Trax</option>
                                        <option value="2">Vendor</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success">Update</button>
                            <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="UploadDocumentModal" data-backdrop="static">
            <div class="modal-dialog modal-md">
                <div class="modal-content">

                    <div class="modal-header bg-primary white">
                        <h4 class="modal-title">Upload Vehicle Document</h4>
                    </div>

                    <form id="upload_document_form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="vehicle_id" id="doc_vehicle_id">

                        <div class="modal-body">
                            <div class="form-group">
                                <label>Document Name</label>
                                <input type="text" name="document_name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Document File</label>
                                <input type="file" name="document_file" class="form-control" required>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success">Upload</button>
                            <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="ViewDocumentsModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-primary white">
                        <h4 class="modal-title">Vehicle Documents</h4>
                    </div>

                    <div class="modal-body">
                        <table class="table table-bordered" id="documents_table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Document Name</th>
                                <th>Document</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-info" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style>

        .selectize-control {
            width:  100%  !important;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function () {


            // ------------------------------------------
            // DATATABLE
            // ------------------------------------------
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                            @if (session('role_id') == 1 || in_array(662, session('permissions')))
                            {
                                text: '<i class="la la-plus"></i> Add Vehicle',
                                className: 'btn btn-primary add_vehicle',
                                enabled: true,
                                action: function (e, dt, node, config) {
                                    $('#AddVehicleModal').modal('show');

                                }
                            },
                           @endif
                        'reset'
                ],
                lengthMenu: [[50,100,500,1000,-1],[50,100,500,1000,'All']],
                pageLength: 50,
                processing: true,
                serverSide: true,
                language: {
                    processing: data_table_loader
                },
                scrollY:'300px',
                scrollX:'100%',
                ajax: "{{ route('admin.cargo.supply_chain.local_fleet.vehicle.list') }}",
                rowId: 'id',
                order: [[1, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'vehicle_number', name: 'local_fleet_vehicles.vehicle_number', class: 'align-middle vehicle_number'},
                    {data: 'created_at', name: 'local_fleet_vehicles.created_at', class: 'align-middle created_at'},
                    {data: 'vehicle_type', name: 'local_fleet_vehicles.vehicle_type', class: 'align-middle vehicle_type'},
                    {data: 'make', name: 'local_fleet_vehicles.make', class: 'align-middle make'},
                    {data: 'city_name', name: 'c.name', class: 'align-middle city_name'},
                    {data: 'vendor_name', name: 'local_fleet_vehicles.vendor_name', class: 'align-middle vendor_name', orderable: false},
                    {data: 'vendor_type', name: 'local_fleet_vehicles.vendor_type', class: 'align-middle vendor_type',orderable: false},
                    {data: 'driver_name', name: 'local_fleet_vehicles.driver_name', class: 'align-middle driver_name',orderable: false},
                    {data: 'capacity', name: 'local_fleet_vehicles.capacity', class: 'align-middle capacity'},
                    {data: 'mileage_per_liter', name: 'local_fleet_vehicles.mileage_per_liter', class: 'align-middle mileage_per_liter'},
                    {data: 'fueling_responsibility', name: 'local_fleet_vehicles.fueling_responsibility', class: 'align-middle fl',orderable: false},
                    {data: 'rent_amount', name: 'local_fleet_vehicles.rent_amount', class: 'align-middle rent_amount'},
                    {data: 'rent_type', name: 'local_fleet_vehicles.rent_type', class: 'align-middle rent_type',orderable: false},
                    {data: 'status', name: 'local_fleet_vehicles.status', class: 'align-middle status',searchable: false},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
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

                    var vehicle_type = '<select name="vehicle_type" id="vehicle_type" class="select2 form-control">';
                    vehicle_type +='<option value="1">Permanent</option>';
                    vehicle_type +='<option value="2">Temporary</option>';
                    vehicle_type +='</select>';

                    var vendor_type_select = '<select name="vendor_type" id="vendor_type" class="select2 form-control">';
                    vendor_type_select +='<option value="1">Vendor</option>';
                    vendor_type_select +='<option value="2">Self</option>';
                    vendor_type_select +='</select>';

                    var rent_type_select = '<select name="rent_type" id="rent_type" class="select2 form-control">';
                    rent_type_select +='<option value="1">Daily</option>';
                    rent_type_select +='<option value="2">Monthly</option>';
                    rent_type_select +='</select>';

                    var fueling_responsibility_select = '<select name="fueling_responsibility" id="fueling_responsibility" class="select2 form-control">';
                    fueling_responsibility_select +='<option value="1">SigTrax</option>';
                    fueling_responsibility_select +='<option value="2">Vendor</option>';
                    fueling_responsibility_select +='</select>';

                    var status_select = '<select name="status" id="status" class="select2 form-control">';
                    status_select +='<option value="1">Active</option>';
                    status_select +='<option value="0">Inactive</option>';
                    status_select +='</select>';



                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if (($(header).is('.serial_number') || $(header).is('.action'))) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.vehicle_type')){
                            $(vehicle_type).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.vendor_type')) {
                            $(vendor_type_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.rent_type')) {
                            $(rent_type_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.fl')) {
                            console.log(fueling_responsibility_select);
                            $(fueling_responsibility_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.status')) {
                            $(status_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
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


                    $('#vehicle_type').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $('#vendor_type').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Vendor Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $('#rent_type').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Rent Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $('#fueling_responsibility').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Responsibility",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $('#status').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });


            // ------------------------------------------
            // EDIT DATA FILL
            // ------------------------------------------
            $('body').on('click','#datatable tbody tr td.action .dropdown-item.edit', function(){

                var id = $(this).parents('tr').attr('id');
                $('#edit_id').val(id);
                $.get("{{ url('admin/cargo/supply_chain/local_fleet/vehicle/edit') }}/" + id, function(res){
                    $('#edit_vehicle_number').val(res.vehicle_number);
                    $('#edit_make').val(res.make);
                    $('#edit_city_id').val(res.city_id).trigger('change');
                    $('#edit_vendor_name').val(res.vendor_name);
                    $('#edit_vendor_type').val(res.vendor_type).trigger('change');
                    $('#edit_driver_name').val(res.driver_name);
                    $('#edit_capacity').val(res.capacity);
                    $('#edit_mileage_per_liter').val(res.mileage_per_liter);
                    $('#edit_vehicle_type').val(res.vehicle_type).trigger('change');
                    $('#edit_rent_type').val(res.rent_type).trigger('change');
                    $('#edit_rent_amount').val(res.rent_amount);
                    $('#edit_fueling_responsibility').val(res.fueling_responsibility).trigger('change');

                    $('#EditVehicleModal').modal('show');
                });

            });

            $('#city_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select City",
                allowClear:true,
                dropdownParent:$('#AddVehicleModal')
            });

            $('#edit_city_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select City",
                allowClear:true,
                dropdownParent:$('#EditVehicleModal')
            });

            $('body').on('click','#datatable tr .generate-barcode', function(){
                var id = parseInt($(this).parents('tr').attr('id'));
                if (isNaN(id) || id <= 0) {
                    var error = 'Vehicle ID Not Found, Please Try again!';
                    toastr.error(error, 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                    return;
                }
                $.ajax
                    ({
                        url: '{!! route('admin.cargo.supply_chain.local_fleet.vehicle.qr_code_print') !!}',
                        method: 'POST',
                        data: {
                            'vehicle_id': id,
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
                        } else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            });

            $('body').on('click', '.upload-document', function () {
                let vehicle_id = $(this).closest('tr').attr('id');
                $('#doc_vehicle_id').val(vehicle_id);
                $('#UploadDocumentModal').modal('show');
            });

            $('#upload_document_form').on('submit', function (e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.cargo.supply_chain.local_fleet.vehicle.document.upload') }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        if (res.status === 0) {
                            toastr.success('Document uploaded successfully');
                            $('#UploadDocumentModal').modal('hide');
                        } else {
                            toastr.error(res.message);
                        }
                    }
                });
            });

            $('body').on('click', '.view-documents', function () {
                let vehicle_id = $(this).closest('tr').attr('id');

                let url = "{{ route('admin.cargo.supply_chain.local_fleet.vehicle.document.list', ':id') }}";
                url = url.replace(':id', vehicle_id);
                $.get(url, function (res) {

                        let html = '';
                        res.data.forEach(function (d, i) {
                            html += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${d.document_name}</td>
                    <td><a href="${d.document_path}" target="_blank">View</a></td>
                    <td>${d.date}</td>
                </tr>`;
                        });

                        $('#documents_table tbody').html(html);
                        $('#ViewDocumentsModal').modal('show');
                    }
                );
            });
        });
    </script>

@endsection
