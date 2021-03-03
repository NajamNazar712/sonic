@extends('admin.layout.master')

@section('title', 'Fuel Management')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Fuel Management
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Card Number</th>
                                    <th class="border-primary border-darken-1">Card Holder</th>
                                    <th class="border-primary border-darken-1">Card Holder Type</th>
                                    <th class="border-primary border-darken-1">Amount</th>
                                    <th class="border-primary border-darken-1">Fuel Type</th>
                                    <th class="border-primary border-darken-1">Fuel Deduction Type</th>
                                    <th class="border-primary border-darken-1">Requested by</th>
                                    <th class="border-primary border-darken-1">Approved by</th>
                                    <th class="border-primary border-darken-1">Approved at</th>
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



    <div class="modal fade" id="FuelCardHolderTypeSelectModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="FuelCardHolderTypeSelectModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Select Fuel Card Holder Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                            <div class="form-group text-center">
                                <select name="card_holder_type" id="card_holder_type" class="form-control select2">
                                    @foreach($card_holder_types as $card_holder_type)
                                        <option value="{{ $card_holder_type->id }}" > {{ $card_holder_type->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="card_holder_type_value_select_btn" class="btn btn-success">Next</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="StaffRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="StaffRequestModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Fuel Card Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" id="fuel_card_request_btn" class="btn btn-success">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="RiderRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="RiderRequestModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Rider Card Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" id="fuel_card_request_btn" class="btn btn-success">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="VehicleRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="VehicleRequestModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Vehicle Card Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" id="fuel_card_request_btn" class="btn btn-success">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                @if (session('role_id') == 1 || in_array(448, session('permissions')))
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    text: 'Add',
                    className: 'btn btn-primary add',
                    text: '<i class="la la-plus"></i> Add',
                    action: function (e, dt, node, config) {
                        $('#card_holder_type').val('')
                        $('#card_holder_type').trigger('change');
                        $('#FuelCardHolderTypeSelectModal').modal('show');
                    }
                },'reset'],
                @else
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: ['reset'],
                @endif
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.user_management.fuel_management.list') }}',
                rowId: 'id',
                order: [[6, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'card_number', name: 'card_number', class: 'align-middle card_number'},
                    {data: 'card_holder', name: 'card_holder', class: 'align-middle card_holder'},
                    {data: 'card_holder_type', name: 'card_holder_type', class: 'align-middle card_holder_type'},
                    {data: 'amount', name: 'amount', class: 'align-middle amount'},
                    {data: 'fuel_type', name: 'fuel_type', class: 'align-middle fuel_type'},
                    {data: 'fuel_deduction_type', name: 'fuel_deduction_type', class: 'align-middle fuel_deduction_type'},
                    {data: 'requested_by', name: 'requested_by', class: 'align-middle requested_by'},
                    {data: 'approved_by', name: 'approved_by', class: 'align-middle approved_by'},
                    {data: 'approved_at', name: 'approved_at', class: 'align-middle approved_at'},
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
                    var card_holder_type_select = '<select name="card_holder_type_select" id="card_holder_type_select" class="select2 form-control"></select>';
                    var fuel_type_select = '<select name="fuel_type_select" id="fuel_type_select" class="select2 form-control"></select>';
                    var fuel_deduction_type_select = '<select name="fuel_deduction_type_select" id="fuel_deduction_type_select" class="select2 form-control"></select>';


                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.card_holder_type')){
                            $(card_holder_type_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.fuel_type')){
                            $(fuel_type_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.fuel_deduction_type')){
                            $(fuel_deduction_type_select).appendTo($(search))
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
                    var card_holder_type_data = $.map({!! $card_holder_types !!}, function (obj) {
                        obj.id = obj.id; // replace name with the property used for the text
                        obj.text = obj.name; // replace name with the property used for the text

                        return obj;
                    });
                    var fuel_type_data = $.map({!! $fuel_types !!}, function (obj) {
                        obj.id = obj.id; // replace name with the property used for the text
                        obj.text = obj.name; // replace name with the property used for the text

                        return obj;
                    });
                    var fuel_deduction_type_data = $.map({!! $fuel_deduction_types !!}, function (obj) {
                        obj.id = obj.id; // replace name with the property used for the text
                        obj.text = obj.name; // replace name with the property used for the text

                        return obj;
                    });
                    $("#card_holder_type_select").prepend('<option value="" selected></option>').select2({
                        data:card_holder_type_data,
                        placeholder: "Select Card Holder Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#fuel_type_select").prepend('<option value="" selected></option>').select2({
                        data:fuel_type_data,
                        placeholder: "Select Fuel Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#fuel_deduction_type_select").prepend('<option value="" selected></option>').select2({
                        data:fuel_deduction_type_data,
                        placeholder: "Select Fuel Deduction Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
            $("#card_holder_type").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Card Holder Type",
                width:'100%',
                dropdownCssClass: 'form-control-sm p-0'
            });

            $('#FuelCardHolderTypeSelectModal #card_holder_type_value_select_btn').on('click',function () {
                    var card_holder_type = $('#card_holder_type').val();
                    if(card_holder_type != '')
                    {
                        $.ajax({
                            url: '{!! route('admin.user_management.fuel_management.create') !!}',
                            method: 'get',
                            data: {
                                'card_holder_type': card_holder_type,
                            }
                        })
                            .done(function (response) {
                                if (response.status == 1) {
                                    $('#FuelCardHolderTypeSelectModal').modal('hide');
                                    html = '';
                                    html += '<select name="card_holder" id="card_holder" class="select2 form-control">';
                                    $.each(response.data, function(index, values) {
                                        html += `<option value="${values.id}" > ${values.name} </option>`;
                                    });
                                    html += '</select>';
                                    $('#StaffRequestModal .modal-body').html(html);

                                    $("#card_holder").prepend('<option value="" selected></option>').select2({
                                        placeholder: "Select Card Holder",
                                        width:'100%',
                                        dropdownCssClass: 'form-control-sm p-0',
                                        allowClear: true,
                                    });

                                    $('#StaffRequestModal').modal('show');
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                            });
                    }

            });

        });
    </script>
@endsection