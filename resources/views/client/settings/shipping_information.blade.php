@extends('client.layout.master')
@section('title','Shipping Information List')

@section('content')
    <h1 class="mb-1">
        Shipping Information List
    </h1>
<div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
            </div>
        </div>
        <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
            <thead>
            <tr role="row" class="bg-primary white">
                <th class="border-primary border-darken-1"></th>
                <th class="border-primary border-darken-1">S.No</th>
                <th class="border-primary border-darken-1">Pickup Address ID</th>
                <th class="border-primary border-darken-1">IBAN</th>
                <th class="border-primary border-darken-1">Pickup Address</th>
                <th class="border-primary border-darken-1">Person of Contact</th>
                <th class="border-primary border-darken-1">Vendor</th>
                <th class="border-primary border-darken-1">Phone Number</th>
                <th class="border-primary border-darken-1">City</th>
                <th class="border-primary border-darken-1">Email Address</th>
                <th class="border-primary border-darken-1">Status</th>

            </tr>
            </thead>
        </table>
</div>

<div class="modal fade text-left" id="AddIBANModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddIBANModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h4 class="modal-title white">Add IBAN</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="add_iban_form" action="{{route('cod.settings.shipping_information.add_iban')}}" method="post" >
                <div class="modal-body text-center">
                    @method('POST')
                    @csrf
                    <div class="container">
                        <input type="hidden" name="pickup_address_ids" id="pickup_address_id">
                        <div class="row justify-content-center">

                            <div class="col-12 form-group">
                                <select name="bank_info_select" id="bank_select" class="form-control select2" data-rule-required="true" data-msg-required="Select A Bank">
                                    @if(count($user_bank_infos) > 0)
                                        @foreach($user_bank_infos as $user_bank_info)
                                            <option value="{{$user_bank_info->id}}">{{$user_bank_info->bank_branch}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div id="bank_info_div" class="d-none">
                                <table class="table table-sm table-bordered mb-1">
                                    <tbody>
                                    <tr role="row">
                                        <th class="border-primary border-darken-1 align-middle text-center">Bank Name</th>
                                        <td class="align-middle text-center" id="b_name"></td>
                                    </tr>
                                    <tr role="row">
                                        <th class="border-primary border-darken-1 align-middle text-center">Bank Branch</th>
                                        <td class="align-middle text-center" id="b_branch"></td>
                                    </tr>
                                    <tr role="row">
                                        <th class="border-primary border-darken-1 align-middle text-center">Account Number</th>
                                        <td class="align-middle text-center" id="b_account_number"></td>
                                    </tr>
                                    <tr role="row">
                                        <th class="border-primary border-darken-1 align-middle text-center">Account Title</th>
                                        <td class="align-middle text-center" id="b_account_title"></td>
                                    </tr>
                                    <tr role="row">
                                        <th class="border-primary border-darken-1 align-middle text-center">IBAN Number</th>
                                        <td class="align-middle text-center" id="b_iban"></td>
                                    </tr>
                                    <tr role="row">
                                        <th class="border-primary border-darken-1 align-middle text-center">City Name</th>
                                        <td class="align-middle text-center" id="b_city"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-3">
                        <button id="add_iban_btn" type="submit" class="btn btn-primary btn-block">Update</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">

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
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            $('#bank_select').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Bank',
                width:'100%',
                dropdownParent:$('#AddIBANModal')
            }).bind('change',function () {
                var id = $(this).val();
                $(this).valid();
                if(id){
                    $.ajax({
                        url: '{{ route('cod.settings.shipping_information.bank_info') }}',
                        method: 'GET',
                        data: {
                            'bank_info_id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status == 0) {
                            $('#bank_info_div').removeClass('d-none');
                            $('#b_name').text(data.details.bank_name);
                            $('#b_branch').text(data.details.bank_branch);
                            $('#b_account_number').text(data.details.account_no);
                            $('#b_account_title').text(data.details.title);
                            $('#b_iban').text(data.details.iban);
                            $('#b_city').text(data.details.city);
                        }
                        else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }

            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('cod.settings.shipping_information.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Pickup Address ID.');
                            head.push('IBAN');
                            head.push('Pickup Address');
                            head.push('Person of Contact');
                            head.push('Vendor');
                            head.push('Phone No.');
                            head.push('City');
                            head.push('Email Address');
                            head.push('Status');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.id);
                                row.push(values.iban);
                                row.push(values.pickup_address);
                                row.push(values.poc);
                                row.push(values.vendor);
                                row.push(values.phone);
                                row.push(values.city_name);
                                row.push(values.email);
                                row.push(values.status);

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
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        text: '<i class="la la-plus"></i> Add IBAN',
                        className: 'btn btn-primary add_iban',
                        enabled : false,
                        action: function (e, dt, node, config) {
                            if (selected_rows.length > 0) {
                                $('#AddIBANModal').modal('show');


                            }else{
                                var error = 'Please select at-least one shipping address!';
                                toastr.error(error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        }
                    },
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action: function (e) {
                            e.preventDefault();

                            table.rows().nodes().each(function (index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.add_iban').enable();

                                }
                            });
                        }
                    },
                    {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action: function (e) {
                            e.preventDefault();

                            table.rows().nodes().each(function (index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.add_iban').disable();

                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Shipping Information List',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
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
                ajax:'{{ route('cod.settings.shipping_information.list') }}',
                rowId: 'id',
                order:[1,'desc'],
                columns: [
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        class: 'text-center align-middle select p-1',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {orderable: false,searchable: false,data: 'serial_number',  name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'id', name: 'id'},
                    {data: 'iban', name: 'ubi.iban'},
                    {data: 'pickup_address', name: 'pickup_address'},
                    {data: 'poc', name: 'poc'},
                    {data: 'vendor', name: 'vendor'},
                    {data: 'phone', name: 'phone'},
                    {data: 'city_name', name: 'c.name'},
                    {data: 'email', name: 'email'},
                    {data: 'status',orderable: false, name: 'status',class:'status'}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                    $('td:eq(0)', row).addClass('select-checkbox');

                    if ($.inArray(data.shipment_id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function() {

                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Disabled</option>' +
                        '<option value="1">Enabled</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.select')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
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
                    this.api().table().columns.adjust();
                }
            });
            $('.datatable tbody').on('click', 'tr td.select-checkbox', function () {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button(0).enable();

                }
                else {
                    table.button(0).disable();
                }
            });

            $( "#add_iban_form" ).validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form){
                    $('#pickup_address_id').val(selected_rows);
                    form.submit();

                }
            });

        });
    </script>
@endsection