@extends('admin.layout.master')

@section('title', 'Retail Users')

@section('content')
    <h1 class="mb-1">
        Retail Users
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Trax ID</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Zone</th>
                        <th class="border-primary border-darken-1">Name</th>
                        <th class="border-primary border-darken-1">Phone Number</th>
                        <th class="border-primary border-darken-1">CNIC</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Category</th>
                        <th class="border-primary border-darken-1">Created Date/Time</th>
                        <th class="border-primary border-darken-1">Created By</th>
                        <th class="border-primary border-darken-1">Updated Date/Time</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_user" role="dialog" aria-labelledby="add_user_title" aria-hidden="true">
        <div class="modal-dialog add_user_modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_remarks_title">Add User</h4>

                    <button type="button" class="close modal_close_btn" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_user_form" class="form-horizontal mb-1 justify-content-center" method="POST" action="{{ route('admin.retail.users.add') }}" novalidate="novalidate" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <select name="store" id="store" class="form-control select2" data-rule-required="true" data-msg-required="Franchise is required" onchange="showForm()">
                                            <option value="1">Franchise</option>
                                            <option value="2">Trax Center</option>
                                        </select>
                                    </div>

                                    <div class="form-group d-none" id="franchise_div">
                                        <select name="franchise" id="franchise" class="form-control select2" data-rule-required="true" data-msg-required="Franchise is required">
                                            @foreach($franchises as $franchise)
                                                <option value="{{$franchise->id}}"> {{$franchise->name}} </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group d-none" id="trax_center_div">
                                        <select name="trax_center" id="trax_center" class="form-control select2" data-rule-required="true" data-msg-required="Trax Center is required">
                                            @foreach($trax_centers as $trax_center)
                                                <option value="{{$trax_center->id}}"> {{$trax_center->name}} </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="name" id="name" class="form-control" placeholder="User Name*" data-rule-required="true" data-msg-required="Name is required" data-rule-remote="{{ route('admin.retail.users.name') }}" data-msg-remote="Name must be unique">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="phone_number" id="phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                                    </div>

                                    <div class="form-group position-relative">
                                        <input type="password" class="form-control" id="password" placeholder="Password*" value="" name="password" data-rule-required="true" data-msg-required="Password is required" data-rule-minlength="6" data-msg-minlength="Password needs to be at-least 6 Characters" autocomplete="nope">
                                        <div class="form-control-position" id="eye">
                                            <i class="la la-eye success"></i>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="cnic" id="cnic" class="form-control cnic" placeholder="CNIC*" data-rule-required="true" data-msg-required="CNIC is required">
                                    </div>

                                    <div class="form-group">
                                        <textarea name="address" id="address" class="form-control address" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required"></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <input type="text" name="trax_id" id="trax_id" class="form-control" placeholder="Trax Id">
                                    </div>
                                    
                                    {{-- <div class="form-group">
                                        <input type="text" name="gst_percentage" id="gst_percentage" class="form-control" placeholder="GST %">
                                    </div> --}}

                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o"></span>
                                        </span>
                                        </div>
                                        <input type="text" name="agreement_start_date" id="delivery_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" placeholder="Joining date*" required>
                                    </div>

                                    <div class="form-group">
                                        <input type="number" name="salary" id="salary" class="form-control" placeholder="Salary">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="family_member_name[]" id="father_name" class="form-control" placeholder="Father Name">
                                    </div>
                                    
                                    <div class="form-group">
                                        <input type="text" name="family_member_name[]" id="mother_name" class="form-control" placeholder="Mother Name">
                                    </div>
                                </div>
                                
                                <div class="col">
                                    <div id="for_trax_users" class="">
                                        <div class="form-group">
                                            <select name="marital_status" id="marital_status" class="select2 form-control">
                                                <option value="" selected disabled>Select Marital Status</option>
                                                <option value="1">Single</option>
                                                <option value="2">Marital</option>
                                            </select>
                                        </div>
    
                                        <div class="form-group marital_details d-none">
                                            <div class="form-group">
                                                <input type="text" name="family_member_name[]" id="spouse_name" class="form-control" placeholder="Spouse Name">
                                            </div>
                                            <div class="form-group">
                                                <input type="date" name="family_member_name[]" id="spouse_dob" class="form-control" placeholder="Spouse DOB">
                                            </div>
                                            <h4 class="">Children</h4>
                                            <div class="row">
    
                                                <div class="col-9" id="child_input_container">
                                                    <div class="child-template d-none">
                                                        <div class="form-group row">
                                                            <div class="col-9">
                                                                <input type="text" class="form-control child_input" placeholder="Child">
                                                            </div>
                                                            <div class="col-3">
                                                                <button type="button" class="btn btn-danger" id="remove_child_column_btn">
                                                                    Remove
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <button type="button" class="btn btn-primary" id="add_child_column_btn">
                                                        Add
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
    
                                        <h4 class="">Commission</h4>
                                        <div class="row">
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <select name="retail_shipping_mode_id[]" id="retail_shipping_mode_id" class="select2 form-control retail_shipping_mode_id" data-rule-required="true" data-msg-required="Please choose a Product">
                                                        @foreach($shipping_modes as $shipping_mode)
                                                            <option value="{{$shipping_mode->id}}">{{$shipping_mode->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
    
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <div class="input-group mb-2">
                                                        <input type="text" name="product_percentage[]" id="product_percentage" class="form-control product_percentage" placeholder="Product"  value="" max="100">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
    
                                            <div class="col-2">
                                                <input type="button" class="btn btn-primary" id="retail_product_add_btn" value="Add">
                                            </div>
                                        </div>
                                        <span id="error_message" class="text-danger"></span>
    
                                        <div class="row" id="tableRow" style="display: none;">
                                            <div class="col">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Selected Option</th>
                                                            <th>Product Percentage</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tableBody">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="attachment_1" id="attachment_1_label">Upload Employee Form*</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_1" id="attachment_1" accept=".doc,.docx,.pdf" data-rule-required="true" data-msg-required="Employee Form is required">
                                    </div>
            
                                    <div class="form-group">
                                        <label for="attachment_2">CNIC front image*</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_2" id="attachment_2" accept="image/*,.doc,.docx,.pdf" data-rule-required="true" data-msg-required="CNIC front image is required">
                                    </div>
            
                                    <div class="form-group">
                                        <label for="attachment_3">CNIC back image*</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_3" id="attachment_3" accept="image/*,.doc,.docx,.pdf" data-rule-required="true" data-msg-required="CNIC back image is required">
                                    </div>
            
                                    <div class="form-group">
                                        <label for="attachment_4">Profile picture</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_4" id="attachment_4" accept="image/*,.doc,.docx,.pdf">
                                    </div>
            
                                    <div class="form-group">
                                        <label for="attachment_5">Attachment 5</label>
                                        <input class="form-control form-control-sm" type="file" name="attachment_5" id="attachment_5" accept="image/*,.doc,.docx,.pdf">
                                    </div>
                                </div>
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

    <div class="modal fade" id="logs_modal" role="dialog" aria-labelledby="logs_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Admin Name</th>
                                <th>Changed Fields</th>
                                <th>Date of change</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Log entries will be injected here by JavaScript -->
                        </tbody>
                    </table>
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
        .add_user_modal{
            max-width: 1300px;
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
            $('#eye').on('mousedown',function(){$('input[name="password"]').attr('type','text')}).on('mouseup',function(){$('input[name="password"]').attr('type','password')});
            $('#peye').on('mousedown',function(){$('input[name="password"]').attr('type','text')}).on('mouseup',function(){$('input[name="password"]').attr('type','password')});

            $('.phone_number').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });

            $('.cnic').inputmask({
                'mask': '99999-9999999-9',
                'clearIncomplete': true
            });

            $('#add_user_form #store').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Category*',
                allowClear:true
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if(id == 1){
                    $('#trax_center_div').addClass('d-none');
                    $('#franchise_div').removeClass('d-none');
                }
                else if(id == 2){
                    $('#trax_center_div').removeClass('d-none');
                    $('#franchise_div').addClass('d-none');
                }
                else{
                    $('#trax_center_div').addClass('d-none');
                    $('#franchise_div').addClass('d-none');
                }
            });

            $('#add_user_form #trax_center').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Trax Center*',
                allowClear:true
            });

            $('#add_user_form #franchise').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Franchise*',
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
                                row.push(values.created);
                                row.push(values.created_by);
                                row.push(values.updated);
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
                    { data:'created' ,name: 'retail_users.created_at', class: 'align-middle text-center created_at'},
                    { data:'created_by' ,name: 'ac.name', class: 'align-middle text-center created_by'},
                    { data:'updated' ,name: 'retail_users.updated_at', class: 'align-middle text-center updated_at'},
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

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.enable', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url: '{!! route('admin.retail.users.status') !!}',
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
                    url: '{!! route('admin.retail.users.status') !!}',
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

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.view_logs', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                if($(this).hasClass('view_logs')) {
                    $.ajax({
                        url: "{{ route('admin.retail.view_logs') }}",
                        method: 'GET',
                        data: {
                            'id': id,
                            'screen_name' : 'Retail User'
                        },
                        success: function (data) {
                            if (data.status == 0) {
                                var logs = data.logs;
                                var logContent = '';
                                if (logs.length > 0) {
                        
                                    logs.forEach(function (log) {
                                        let editedFieldsFormatted = log.data
                                            .split(', ')
                                            .map(field => {
                                                // Split the field at "->", keep the "->" and make the part after it bold
                                                let parts = field.split('->');
                                                if (parts.length > 1) {
                                                    return parts[0] + ' <strong>' + '-> ' + parts[1].trim() + '</strong>';
                                                }
                                                return field; // Return as is if "->" is not found
                                            })
                                            .join('<br>'); // Add line breaks between fields

                                        logContent += '<tr>';
                                        logContent += '<td>' + log.name + '</td>';
                                        logContent += '<td>' + editedFieldsFormatted + '</td>';
                                        logContent += '<td>' + log.created_at + '</td>';
                                        logContent += '</tr>';
                                    });

                                    $('#logs_modal table tbody').html(logContent);
                                    $('#logs_modal').modal('show');
                                } else {
                                    toastr.error('No logs found for this record.', 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                            } else {
                                toastr.error(data.message, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        },
                        error: function () {
                            // Handle errors in the AJAX request
                            toastr.error('Something went wrong while retrieving logs.', 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });

            $('#add_user').on('hide.bs.modal', function () {
                $('#hub').val(null).trigger('change');
                $('#name').val('');
                $('#phone_number').val('');
                $('#email').val('');
                $('#cnic').val('');
                $('#lat').val('');
                $('#long').val('');
            });

            $('#add_user_form').validate({
                ignore: ":not(:visible),:disabled",
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    var retailShippingIds = [];
                    var productPercentages = [];
                    $("#tableBody").find("tr").each(function() {
                        var selectedOption = $(this).find("td:first").text();
                        var productPercentage = $(this).find("td:nth-child(2)").text();
                        retailShippingIds.push(selectedOption);
                        productPercentages.push(productPercentage);
                    });
                    $(form).append("<input type='hidden' name='retail_shipping_mode_id' value='" + JSON.stringify(retailShippingIds) + "'>");
                    $(form).append("<input type='hidden' name='product_percentage' value='" + JSON.stringify(productPercentages) + "'>");

                    swal({
                        title: 'Please Wait!',
                        text: 'User is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });

            $('#add_user_form #delivery_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#add_trax_center_form #delivery_date_to').pickadate('picker').set('min', $('#add_trax_center_form #delivery_date_from').pickadate('picker').get('select'));
                    }
                }
            });

            $("#marital_status").on('change', function(){
                var selectedOption = $(this).val();
                if (selectedOption == 1){
                    $(".marital_details").addClass("d-none");
                    $("#spouse_name").val('');
                    $("#spouse_dob").val('');
                    // $("#wife_data").val('');
                    $(".child_input").val('');
                } else if(selectedOption == 2) {
                    $(".marital_details").removeClass("d-none");
                }
            });

            var childCount = 0;
            function addChildInfo() {
                // Update the number to increase child count
                if (childCount < 4){
                    var $childInput = $('.child-template').clone().removeClass('child-template').removeClass('d-none');
                    childCount++;
                    $childInput.find('.child_input').attr({
                        'id': 'child_' + childCount + '_data',
                        'name': 'family_member_name[]',
                        'placeholder': 'Child name'
                    });
                    $('#child_input_container').append($childInput);
                }
            }
            $("#add_child_column_btn").on('click', function () {
                addChildInfo();
            });

            $(document).on('click', '#remove_child_column_btn', function () {
                var $formGroup = $(this).closest('.form-group');
                var $inputField = $(this).closest('.form-group').find('.child_input');
                var inputId = $inputField.attr('id');
                $inputField.val('');
                $formGroup.remove();
                if (childCount > 0) {
                    childCount--;
                }
            });

            function resetModal() {
                $("#product_percentage").val("");
                $("#retail_shipping_mode_id").val("1").trigger("change");
                $("#error_message").hide();
                $("#product_percentage").removeAttr("required");
                $("#tableBody").empty();
                $("#tableRow").hide();
            }

            $("#retail_product_add_btn").on('click', function (event) {
                var selectedOption = $("#retail_shipping_mode_id option:selected").text();
                var productPercentage = $("#product_percentage").val();

                if (productPercentage.trim() === '' || !$.isNumeric(productPercentage)) {
                    $("#error_message").text("Please enter a valid product percentage.").show();
                    $("#product_percentage").attr("required", true);
                } else {
                    $("#error_message").hide();
                    $("#product_percentage").removeAttr("required");
                    var isDuplicate = false;
                    $("#tableBody").find("tr").each(function() {
                        if ($(this).find("td:first").text() === selectedOption) {
                            isDuplicate = true;
                            return false; // Exit the loop if a duplicate is found
                        }
                    });

                    if (isDuplicate) {
                        $("#error_message").text("Error: Cannot add same product.").show();
                    } else {
                        var newRow = $("<tr><td>" + selectedOption + "</td><td>" + productPercentage + "%</td><td><button class='btn btn-danger btn-sm remove-item'>Remove</button></td></tr>");
                        $("#tableBody").append(newRow);
                        newRow.find('.remove-item').click(function() {
                            $(this).closest("tr").remove();
                            if ($("#tableBody").find("tr").length === 0) {
                                $("#tableRow").hide();
                            }
                        });
                        $("#tableRow").show();
                        $("#product_percentage").val('');
                    }
                }
            });

            $(".modal_close_btn").click(function() {
                resetModal();
            });

            $('#store').on('change', function(){
                var category = $(this).val();
                if (category == 1) {
                    $('#trax_id').addClass("d-none");
                    $('#salary').addClass("d-none");
                    $('#for_trax_users').addClass("d-none");
                } else {
                    $('#trax_id').removeClass("d-none");
                    $('#trax_id').on('input', function(event) {
                        $(this).val(function(_, value) {
                            return value.replace(/\D/g, '');
                        });
                    });
                    $('#salary').removeClass("d-none");
                    $('#for_trax_users').removeClass("d-none");
                }
            });
        });

        $("#editRetailUser").on("show.bs.modal", function(e) {
            var $invoker = $(e.relatedTarget);
            var action = $invoker.attr('rel');
            var id = $(e.relatedTarget).data('target-id');
            

            if(action == 'editretailuser'){
                $.get( "/admin/retail/users/edit/"+id+"", function( data ) {
                    $("#editRetailUserDiv").html(data);
                });
            }
        });

        function showForm() {
            var selectedValue = document.getElementById("store").value;
            var attachment_1_label = document.getElementById("attachment_1_label");

            var selectedValue = document.getElementById("store").value;
            var dateInput = document.getElementById("delivery_date_from");

            if (selectedValue == 1) {
                attachment_1_label.textContent = "Franchise Agreement Form*";
                dateInput.placeholder = "Agreement Start date";
                // dateInput.removeAttribute('required');
                dateInput.setAttribute('data-msg-required', "Franchise Agreement Form is required");
            } else if (selectedValue == 2) {
                attachment_1_label.textContent = "Upload Employee Form*";
                dateInput.placeholder = "Joining date";
                dateInput.setAttribute('required', true);
                dateInput.setAttribute('data-msg-required', "Employee Form is required");
            }
        }

    </script>
@endsection