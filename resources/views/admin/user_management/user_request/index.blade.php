@extends('admin.layout.master')

@section('title', 'User Request')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    User Request
                </h1>
                <div class="modal fade text-left" id="viewdetails" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ViewDetails"
                     aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary white">
                                <h4 class="modal-title white">View Details </h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <label for="usr" class="font-weight-bold">Name</label>
                                        <div class="form-group">
                                            <input type="text" name="name" id="name" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <label for="phone" class="font-weight-bold">Phone</label>
                                        <div class="form-group">
                                            <input type="text" name="phone_number" id="phone_number" class="form-control" readonly>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <label for="cnic" class="font-weight-bold">CNIC</label>
                                        <div class="form-group">
                                            <input type="text" name="cnic" id="cnic" class="form-control" readonly>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <label for="email" class="font-weight-bold">Department</label>
                                        <div class="form-group">
                                            <input type="text" name="department" id="department" class="form-control" readonly>
                                        </div>
                                    </div>


                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <label for="email" class="font-weight-bold">Email</label>
                                        <div class="form-group">
                                            <input type="email" name="email" id="email" class="form-control" readonly>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <label for="email" class="font-weight-bold">Hub</label>
                                        <div class="form-group">
                                            <input type="text" name="default_hub" id="default_hub" class="form-control" readonly>
                                        </div>
                                    </div>


                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <label for="designation" class="font-weight-bold">Designation</label>
                                        <div class="form-group">
                                            <input type="text" name="designation" id="designation" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <label for="tarx_id" class="font-weight-bold">Employee Id</label>
                                        <div class="form-group">
                                            <input type="text" name="trax_id" id="trax_id" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade text-left" id="AssignHubModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddTierModal"
                     aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary white">
                                <h4 class="modal-title white">Assign Hubs</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade text-left" id="VerifyInfoModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="VerifyInfoModal"
                     aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary white">
                                <h4 class="modal-title white">Verify Account</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">

                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Employee ID</th>
                                    <th class="border-primary border-darken-1">Name</th>
                                    <th class="border-primary border-darken-1">Email</th>
                                    <th class="border-primary border-darken-1">Outlook Email Required (Yes/No)</th>
                                    <th class="border-primary border-darken-1">Phone Number</th>
                                    <th class="border-primary border-darken-1">CNIC</th>
                                    <th class="border-primary border-darken-1">Department</th>
                                    <th class="border-primary border-darken-1">Designation</th>
                                    <th class="border-primary border-darken-1">Default Hub</th>
                                    <th class="border-primary border-darken-1">Request Added By</th>
                                    <th class="border-primary border-darken-1">Request Created At</th>
                                    <th class="border-primary border-darken-1">Verified By HR</th>
                                    <th class="border-primary border-darken-1">Verified By HR At</th>
                                    <th class="border-primary border-darken-1">Requested From Days (TAT)</th>
                                    <th class="border-primary border-darken-1">Forwarded By</th>
                                    <th class="border-primary border-darken-1">Forwarded At</th>
                                    <th class="border-primary border-darken-1">Verified From Date(TAT)</th>
                                    <th class="border-primary border-darken-1">Status</th>
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
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#user_form #department').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Department*'
            });

            $('#user_form #default_hub').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Default Hub*'
            });
            $('#hub_select').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.user_management.user_requests.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Employee Id');
                            head.push('Name');
                            head.push('Email');
                            head.push('OutLook Email Required (Yes/No)');
                            head.push('Phone Number');
                            head.push('CNIC');
                            head.push('Department');
                            head.push('Designation');
                            head.push('Default Hub');
                            head.push('Request Added By');
                            head.push('Request Created At');
                            head.push('Verified By HR');
                            head.push('Verified By HR At');
                            head.push('Requested From Date (TAT)');
                            head.push('Forwarded By');
                            head.push('Forwarded At');
                            head.push('Verified From Date (TAT)');
                            head.push('Status');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.name);
                                row.push(values.email);
                                row.push(values.outlook_email);
                                row.push(values.phone_number);
                                row.push(values.cnic);
                                row.push(values.department);
                                row.push(values.designation);
                                row.push(values.default_hub);
                                row.push(values.request_craeted_by);
                                row.push(values.request_created_at);
                                row.push(values.verified_by_hr);
                                row.push(values.verified_by_hr_at);
                                row.push(values.launched_to_date);
                                row.push(values.forwarded_by);
                                row.push(values.forwarded_at);
                                row.push(values.verified_from_date);
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

                buttons: [{
                    text: '<i class="la la-user-plus"></i> Add',
                    className: 'btn btn-primary add',
                    action: function (e, dt, node, config) {
                        window.location = '{{ route('admin.cancelled_shipments.add.index') }}';
                    }
                },{
                        extend: 'excel',
                        title: 'User Requests',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],

                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },

                serverSide: true,
                ajax: '{{ route('admin.user_management.user_requests.list') }}',
                rowId: 'id',
                order: [[11, 'desc']],
                columns: [
                    //{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'trax_id', name: 'admin_user_requests.trax_id', class: 'align-middle trax_id'},
                    {data: 'name', name: 'admin_user_requests.name', class: 'align-middle name'},
                    {data: 'email', name: 'admin_user_requests.email', class: 'align-middle email'},
                    {data: 'outlook_email', name: 'admin_user_requests.outlook_email', class: 'align-middle outlook_email'},
                    {data: 'phone_number', name: 'admin_user_requests.phone_number', class: 'align-middle phone_number'},
                    {data: 'cnic', name: 'admin_user_requests.cnic', class: 'align-middle cnic'},
                    {data: 'department', name: 'ad.name', class: 'align-middle department'},
                    {data: 'designation', name: 'admin_user_requests.designation', class: 'align-middle designtaion'},
                    {data: 'default_hub', name: 'c.name', class: 'align-middle default_hub'},
                    {data: 'request_craeted_by', name: 'a.name', class: 'align-middle request_craeted_by'},
                    {data: 'request_created_at', name: 'admin_user_requests.request_created_at', class: 'align-middle request_created_at'},
                    {data: 'verified_by_hr', name: 'as.name', class: 'align-middle verified_by_hr'},
                    {data: 'verified_by_hr_at', name: 'admin_user_requests.verified_by_hr_at', class: 'align-middle verified_by_hr_at'},
                    {data: 'requested_from_date', name: 'requested_from_date', class: 'align-middle requested_from_date', orderable: false, searchable: false},
                    {data: 'forwarded_by', name: 'ac.name', class: 'align-middle forwarded_by', orderable: false, searchable: false},
                    {data: 'forwarded_at', name: 'admin_user_requests.forwarded_at', class: 'align-middle forwarded_at', orderable: false, searchable: false},
                    {data: 'verified_from_date', name: 'verified_from_date', class: 'align-middle verified_from_date', orderable: false, searchable: false},
                    {data: 'status', name: 'admin_user_requests.status', class: 'align-middle status'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                     var info = table.page.info();

                     $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                    var info = table.page.info();

                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Requested</option>' +
                        '<option value="1">HR Verified</option>' +
                        '<option value="2">Admin Verified</option>' +
                        '<option value="3">Request Completed</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.select-checkbox')) {
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

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                if ($(this).hasClass('verify')) {
                    var link = '{{ route('admin.user_management.user_requests.verify.index', ["id" => 0]) }}';
                    window.location = link.substr(0, link.lastIndexOf('/')) + '/' + id;
                }
            });
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                if ($(this).hasClass('addrole')) {
                    var link = '{{ route('admin.user_management.user_requests.save.index', ["id" => 0]) }}';
                    window.location = link.substr(0, link.lastIndexOf('/')) + '/' + id;
                }
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                if ($(this).hasClass('forward')) {
                    $.ajax({
                        url: '{!! route('admin.user_management.user_requests.forward') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                    .done(function (data) {
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });
                    });
                }
            });
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {

                var name = table.row( $(this).parents('tr') ).data().name;
                var trax_id = table.row( $(this).parents('tr') ).data().trax_id;
                var email = table.row( $(this).parents('tr') ).data().email;
                var phone_number = table.row( $(this).parents('tr') ).data().phone_number;
                var cnic = table.row( $(this).parents('tr') ).data().cnic;
                var department = table.row( $(this).parents('tr') ).data().department;
                var designation = table.row( $(this).parents('tr') ).data().designation;
                var default_hub = table.row( $(this).parents('tr') ).data().default_hub;

                $('#name').val(name);
                $('#trax_id').val(trax_id);
                $('#email').val(email);
                $('#phone_number').val(phone_number);
                $('#cnic').val(cnic);
                $('#department').val(department);
                $('#designation').val(designation);
                $('#default_hub').val(default_hub);

                if ($(this).hasClass('details')) {
                    $('#viewdetails').modal('show');
                }
            });


            $( "#assign_hub_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Multiple Hub has been assigned!',
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