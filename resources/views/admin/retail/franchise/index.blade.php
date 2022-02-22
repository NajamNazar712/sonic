@extends('admin.layout.master')

@section('title', 'Franchise')

@section('content')
    <h1 class="mb-1">
        Franchise
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Name</th>
                        <th class="border-primary border-darken-1">Phone Number</th>
                        <th class="border-primary border-darken-1">Email</th>
                        <th class="border-primary border-darken-1">CNIC</th>
                        <th class="border-primary border-darken-1">Default Hub</th>
                        <th class="border-primary border-darken-1">Created Date/Time</th>
                        <th class="border-primary border-darken-1">Updated Date/Time</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Franchise Code</th>
                        <th class="border-primary border-darken-1">Location</th>
                        <th class="border-primary border-darken-1">Discount</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_franchise" role="dialog" aria-labelledby="add_franchise_title" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_remarks_title">Add Franchise</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_franchise_form" class="form-horizontal mb-1 justify-content-center" method="POST" action="{{ route('admin.retail.franchise.add') }}" novalidate="novalidate">
                        {{ csrf_field()  }}
                        <div class="form-group">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Franchise Name*" data-rule-required="true" data-msg-required="Name is required" data-rule-remote="{{ route('admin.retail.franchise.name') }}" data-msg-remote="Name must be unique">
                        </div>
                        <div class="form-group">
                            <input type="text" name="phone_number" id="phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" id="email" class="form-control" placeholder="Email*" data-rule-required="true" data-msg-required="Email is required" value="" autocomplete="nope">
                        </div>
                        <div class="form-group">
                            <input type="text" name="cnic" id="cnic" class="form-control cnic" placeholder="CNIC*" data-rule-required="true" data-msg-required="CNIC is required">
                        </div>
                        <div class="form-group">
                            <select name="hub" id="hub" class="form-control select2" data-rule-required="true" data-msg-required="Default Hub is required">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}"> {{$hub->name}} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" name="lat" id="lat" class="form-control lat" placeholder="Latitude*" data-rule-required="true" data-msg-required="Latitude is required">
                        </div>
                        <div class="form-group">
                            <input type="text" name="long" id="long" class="form-control long" placeholder="Longitude*" data-rule-required="true" data-msg-required="Longitude is required">
                        </div>
                       {{-- <div class="form-group">
                            <input type="number" name="discount" id="discount" class="form-control discount" placeholder="Discount" max="100">
                        </div>--}}
                        <div class="input-group mb-3">
                            <input type="text" name="discount" id="discount" class="form-control discount" placeholder="Discount"  value="" max="100">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2">%</span>
                            </div>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_franchise" role="dialog" aria-labelledby="edit_franchise_title" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="edit_remarks_title">Edit Franchise</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="edit_franchise_form" class="form-horizontal mb-1 justify-content-center" method="POST" action="{{ route('admin.retail.franchise.edit') }}" novalidate="novalidate">
                        {{ csrf_field()  }}
                        <input type="hidden" name="franchise_id" id="franchise_id" value="">
                        <div class="form-group">
                            <input type="text" name="name" id="edit_name" class="form-control" placeholder="Franchise Name*" data-rule-required="true" data-msg-required="Name is required" value="">
                        </div>
                        <div class="form-group">
                            <input type="text" name="phone_number" id="edit_phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required" value="">
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" id="edit_email" class="form-control" placeholder="Email*" data-rule-required="true" data-msg-required="Email is required" value="">
                        </div>
                        <div class="form-group">
                            <input type="text" name="cnic" id="edit_cnic" class="form-control cnic" placeholder="CNIC*" data-rule-required="true" data-msg-required="CNIC is required" value="">
                        </div>
                        <div class="form-group">
                            <input type="text" name="lat" id="edit_lat" class="form-control lat" placeholder="Latitude*" data-rule-required="true" data-msg-required="Latitude is required" value="">
                        </div>
                        <div class="form-group">
                            <input type="text" name="long" id="edit_long" class="form-control long" placeholder="Longitude*" data-rule-required="true" data-msg-required="Longitude is required" value="">
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" name="discount" id="edit_discount" class="form-control edit_discount" placeholder="Discount"  value="" max="100">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2">%</span>
                            </div>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="edit" class="btn btn-primary edit" value="Add">Edit</button>
                        </div>
                    </form>
                </div>

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
    <style>
        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
        .show_active{
            -webkit-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -moz-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        span.font-13{
            font-size: 13px;
        }
    </style>

@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $('.phone_number').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });

            $('.cnic').inputmask({
                'mask': '99999-9999999-9',
                'clearIncomplete': true
            });

            $('#add_franchise_form #hub').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Default Hub',
                allowClear:true
            });



            $('.lat').inputmask({
                'alias': 'decimal',
                'allowMinus': true,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 6,
            });
            $('.long').inputmask({
                'alias': 'decimal',
                'allowMinus': true,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 6,
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.retail.franchise.list') }}',
                        data: params,
                        success: function (result) {

                            head = [];

                            head.push('S. No.');
                            head.push('Name');
                            head.push('Phone Number');
                            head.push('Email');
                            head.push('CNIC');
                            head.push('Default Hub');
                            head.push('Created Date/Time');
                            head.push('Updated Date/Time');
                            head.push('Updated By');
                            head.push('Status');
                            head.push('Franchise Code');
                            head.push('Discount');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.phone_no);
                                row.push(values.email);
                                row.push(values.cnic);
                                row.push(values.default_hub);
                                row.push(values.created_at);
                                row.push(values.updated_at);
                                row.push(values.updated_by);
                                row.push(values.status);
                                row.push(values.code);
                                row.push(values.discount);
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
                        @if (session('role_id') == 1 || in_array(434, session('permissions')))
                    {
                        text: 'Add Franchise',
                        className: 'btn btn-primary add',
                        action: function (e, dt, node, config) {
                            $('#add_franchise').modal('show');
                        }
                    },
                        @endif
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Franchise',
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
                    url: '{{ route('admin.retail.franchise.list') }}',
                },
                order: [[7, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'name' ,name: 'retail_franchises.name', class: 'align-middle text-center name'},
                    { data:'phone_no' ,name: 'retail_franchises.phone_no', class: 'align-middle text-center phone_no'},
                    { data:'email' ,name: 'retail_franchises.email', class: 'align-middle text-center email'},
                    { data:'cnic' ,name: 'retail_franchises.cnic', class: 'align-middle text-center cnic'},
                    { data:'default_hub' ,name: 'c.name', class: 'align-middle text-center default_hub'},
                    { data:'created_at' ,name: 'retail_franchises.created_at', class: 'align-middle text-center created_at'},
                    { data:'updated_at' ,name: 'retail_franchises.updated_at', class: 'align-middle text-center updated_at'},
                    { data:'updated_by' ,name: 'a.name', class: 'align-middle text-center updated_by'},
                    { data:'status' ,name: 'retail_franchises.status', class: 'align-middle text-center status'},
                    { data:'code' ,name: 'retail_franchises.code', class: 'align-middle text-center code'},
                    { data:'location' ,name: 'location', class: 'align-middle text-center location', orderable: false, searchable: false},
                    { data:'discount' ,name: 'discount', class: 'align-middle text-center discount', orderable: false, searchable: false},
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

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.location')) {
                            $(td).appendTo($(search));
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
                    this.api().table().columns.adjust();
                }
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.enable', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url: '{!! route('admin.retail.franchise.status') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id,
                        'status': 1
                    }
                }).done(function(data){
                    if(data.status){
                        table.draw(true);
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.disable', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url: '{!! route('admin.retail.franchise.status') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id,
                        'status': 0
                    }
                }).done(function(data){
                    if(data.status){
                        table.draw(true);
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });
            });

            $('#add_franchise').on('hide.bs.modal', function () {
                $('#hub').val(null).trigger('change');
                $('#name').val('');
                $('#phone_number').val('');
                $('#email').val('');
                $('#cnic').val('');
                $('#lat').val('');
                $('#long').val('');
                $('#discount').val('');
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.edit', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                var name = table.row($(this).parents('tr')).data().name;
                var phone_no = table.row($(this).parents('tr')).data().phone_no;
                var cnic = table.row($(this).parents('tr')).data().cnic;
                var email = table.row($(this).parents('tr')).data().email;
                var default_hub_id = table.row($(this).parents('tr')).data().default_hub_id;
                var lat = table.row($(this).parents('tr')).data().location_latitude;
                var long = table.row($(this).parents('tr')).data().location_longitude;
                var discount = table.row($(this).parents('tr')).data().discount;

                $('#franchise_id').val(id);
                $('#edit_name').val(name);
                $('#edit_phone_number').val(phone_no);
                $('#edit_cnic').val(cnic);
                $('#edit_email').val(email);
                $('#edit_lat').val(lat);
                $('#edit_long').val(long);
                $('#edit_discount').val(discount);

                $('#edit_remarks_title').text('Edit Franchise ' + name);
                $('#edit_franchise').modal('show');
            });

            $('#add_franchise_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Franchise is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });

            $('#edit_franchise_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Franchise is being Updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });
        });

    </script>
@endsection