@extends('admin.layout.master')

@section('title', 'Retail Users History')

@section('content')
<h1 class="mb-1">
    {{ $retail_user }} History
</h1>

<div class="card">
    <div class="card-content" aria-expanded="true">
        <div class="card-body">

            <table class="table table-bordered datatable" id="#datatable" style="z-index: 3;">
                <thead>
                <tr role="row" class="bg-primary white">
                    <th class="border-primary border-darken-1">Trax Center Name</th>
                    <th class="border-primary border-darken-1">Trax Center Code</th>
                    <th class="border-primary border-darken-1">Joining Date</th>
                    <th class="border-primary border-darken-1">Last Date</th>
                    <th class="border-primary border-darken-1">Product Name</th>
                    <th class="border-primary border-darken-1">Product Commission</th>
                    <th class="border-primary border-darken-1">Booking Date</th>
                </tr>
                </thead>

                <tbody>
                    @foreach ($retail_user_history as $history)
                        <tr>
                            <td>{{ $history->trax_center_name }}</td>
                            <td>{{ $history->trax_center_code }}</td>
                            <td>{{ $history->joining_date }}</td>
                            <td>{{ $history->last_date ?? '-' }}</td>
                            <td>{{ $history->retail_shipping_mode_name }}</td>
                            <td>{{ $history->product_commission }}</td>
                            <td>{{ $history->booking_date }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    {{-- <script type="text/javascript">
        $(document).ready(function () {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.retail.users.list') }}',
                        data: params,
                        success: function (result) {

                            head = [];

                            head.push('S. No.');
                            head.push('Trax ID');
                            head.push('City');
                            head.push('Hub');
                            head.push('Zone');
                            head.push('Name');
                            head.push('Phone Number');
                            head.push('CNIC');
                            head.push('Address');
                            head.push('Category');
                            head.push('Created Date/Time');
                            head.push('Created By');
                            head.push('Updated Date/Time');
                            head.push('Updated By');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.city);
                                row.push(values.hub);
                                row.push(values.zone);
                                row.push(values.name);
                                row.push(values.phone_no);
                                row.push(values.cnic);
                                row.push(values.address);
                                row.push(values.category);
                                row.push(values.created_at);
                                row.push(values.created_by);
                                row.push(values.updated_at);
                                row.push(values.updated_by);
                                row.push(values.status);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header:head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                // autoWidth: false,
                buttons: [
                        @if (session('role_id') == 1 || in_array(475, session('permissions')))
                    {
                        text: 'Add User',
                        className: 'btn btn-primary add',
                        action: function (e, dt, node, config) {
                            $('#add_user').modal('show');
                        }
                    },
                        @endif
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Retail Users',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.retail.users.list') }}',
                },
                order: [[11, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'trax_id' ,name: 'retail_users.trax_id', class: 'align-middle text-center trax_id'},
                    { data:'city' ,name: 'c.name', class: 'align-middle text-center city'},
                    { data:'hub' ,name: 'h.name', class: 'align-middle text-center hub'},
                    { data:'zone' ,name: 'z.name', class: 'align-middle text-center zone'},
                    { data:'name' ,name: 'retail_users.name', class: 'align-middle text-center name'},
                    { data:'phone_no' ,name: 'retail_users.phone_no', class: 'align-middle text-center phone_no'},
                    { data:'cnic' ,name: 'retail_users.cnic', class: 'align-middle text-center cnic'},
                    { data:'address' ,name: 'retail_users.address', class: 'align-middle text-center address'},
                    { data:'category' ,name: 'retail_users.category', class: 'align-middle text-center category'},
                    { data:'created_at' ,name: 'retail_users.created_at', class: 'align-middle text-center created_at'},
                    { data:'created_by' ,name: 'ac.name', class: 'align-middle text-center created_by'},
                    { data:'updated_at' ,name: 'retail_users.updated_at', class: 'align-middle text-center updated_at'},
                    { data:'updated_by' ,name: 'au.name', class: 'align-middle text-center updated_by'},
                    { data:'status' ,name: 'retail_users.status', class: 'align-middle text-center status'},
                    { data:'action' ,name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false},
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
                        '<option value="1">Active</option>' +
                        '<option value="0">In-Active</option>' +
                        '</select>';
                    var category_select = '<select name="status_select" id="category_select" class="select2 form-control">' +
                        '<option value="1">Franchise</option>' +
                        '<option value="2">Trax Owned</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.category')){
                            $(category_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.status')){
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

                    $("#category_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Category",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
        });
    </script> --}}
@endsection