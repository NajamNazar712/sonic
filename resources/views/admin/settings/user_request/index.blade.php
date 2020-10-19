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
                            <div class="modal-body text-center">

                               {{-- <form id="assign_hub_form" action="{{route('admin.user_management.users.assign_hubs')}}" method="post">
                                    @method('POST')
                                    @csrf
                                    <div class="container">
                                        <input type="hidden" name="id"/>
                                        <div class="col-12 form-group">
                                            <select name="hubs[]" id="hub_select" class="form-control select2" multiple="multiple">
                                                @foreach($hubs as $hub)
                                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="row justify-content-center">
                                            <div class="col-6">
                                                <button id="edit" type="submit" class="btn btn-primary btn-block">Assign Hub</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>--}}
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
                            <div class="modal-body text-center">
                                 <form id="assign_hub_form" action="{{route('admin.user_management.users.assign_hubs')}}" method="post">
                                     @method('POST')
                                     @csrf
                                     {{ csrf_field() }}

                                     <div class="row">
                                         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                             <div class="form-group">
                                                 <input type="text" name="name" class="form-control" placeholder="Name*" data-rule-required="true" data-msg-required="Name is required">
                                             </div>
                                         </div>

                                         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                             <div class="form-group">
                                                 <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                                             </div>
                                         </div>

                                         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                             <div class="form-group">
                                                 <input type="text" name="cnic" id="cnic" class="form-control" placeholder="CNIC*" data-rule-required="true" data-msg-required="CNIC is required">
                                             </div>
                                         </div>

                                         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                             <div class="form-group">
                                                 <input type="email" name="email" class="form-control" placeholder="Email*" data-rule-required="true" data-msg-required="Email is required" data-rule-remote="{{ route('admin.settings.user_requests.email') }}" data-msg-remote="Email must be unique">
                                             </div>
                                         </div>


                                         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                             <div class="form-group">
                                                 <select name="department" class="select2" id="department" data-rule-required="true" data-msg-required="Department is required">
                                                     @foreach($departments as $department)
                                                         <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                     @endforeach
                                                 </select>
                                             </div>
                                         </div>
                                         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                             <div class="form-group">
                                                 <select name="default_hub" class="select2" id="default_hub" data-rule-required="true" data-msg-required="Default hub is required">
                                                     @foreach($hubs as $hub)
                                                         <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                                     @endforeach
                                                 </select>
                                             </div>
                                         </div>

                                         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                             <div class="form-group">
                                                 <input type="text" name="designation" class="form-control" placeholder="Designation*" data-rule-required="true" data-msg-required="Designation is required">
                                             </div>
                                         </div>
                                         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                             <div class="form-group">
                                                 <input type="text" name="trax_id" id="trax_id" class="form-control" placeholder="Trax ID" data-rule-remote="{{ route('admin.settings.user_requests.trax_id') }}">
                                             </div>
                                         </div>
                                         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                             <div class="form-group">
                                                 <input type="password" name="password" id="password" class="form-control" placeholder="Password ">
                                             </div>
                                         </div>
                                     </div>
                                 </form>
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
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Trax ID</th>
                                    <th class="border-primary border-darken-1">Name</th>
                                    <th class="border-primary border-darken-1">Email</th>
                                    <th class="border-primary border-darken-1">Phone Number</th>
                                    <th class="border-primary border-darken-1">CNIC</th>
                                    <th class="border-primary border-darken-1">Department</th>
                                    <th class="border-primary border-darken-1">Designation</th>
                                    <th class="border-primary border-darken-1">Default Hub</th>
                                    <th class="border-primary border-darken-1">Request Added By</th>
                                    <th class="border-primary border-darken-1">Request Created At</th>
                                    <th class="border-primary border-darken-1">Verified By HR</th>
                                    <th class="border-primary border-darken-1">Verified By HR At</th>
                                    <th class="border-primary border-darken-1">Launched To Date (TAT)</th>
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

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.user_requests.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Trax Id');
                            head.push('Name');
                            head.push('Email');
                            head.push('Phone Number');
                            head.push('CNIC');
                            head.push('Department');
                            head.push('Designation');
                            head.push('Default Hub');
                            head.push('Request Added By');
                            head.push('Request Created At');
                            head.push('Verified By HR');
                            head.push('Verified By HR At');
                            head.push('Launched To Date (TAT)');
                            head.push('Status');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.name);
                                row.push(values.email);
                                row.push(values.phone_number);
                                row.push(values.cnic);
                                row.push(values.department);
                                row.push(values.designation);
                                row.push(values.default_hub);
                                row.push(values.request_added_by);
                                row.push(values.request_created_at);
                                row.push(values.verified_by_hr);
                                row.push(values.verified_by_hr_at);
                                row.push(values.launched_to_date);
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
                        window.location = '{{ route('admin.settings.user_requests.add.index') }}';
                    }
                },{
                    text: '<i class="la la-cogs"></i> Assign Hub(s)',
                    className: 'btn btn-primary assign',
                    enabled:false,
                    action: function (e, dt, node, config) {

                        $('input:hidden[name=id]').val(selected_rows);
                        $('#AssignHubModal').modal('show');

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

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.assign').enable();
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

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.assign').disable();
                                    }
                                }
                            });
                        }
                    },{
                        extend: 'excel',
                        title: 'Users',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],

               {{-- buttons: [{
                    extend: 'excel',
                    title: 'Users',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                @endif--}}
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                serverSide: true,
                ajax: '{{ route('admin.settings.user_requests.list') }}',
                rowId: 'id',
                order: [[1, 'asc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'trax_id', name: 'admin_user_requests.trax_id ', class: 'align-middle trax_id'},
                    {data: 'name', name: 'admin_user_requests.name ', class: 'align-middle name'},
                    {data: 'email', name: 'admin_user_requests.email', class: 'align-middle email'},
                    {data: 'phone_number', name: 'admin_user_requests.phone_number', class: 'align-middle phone_number'},
                    {data: 'cnic', name: 'admin_user_requests.cnic', class: 'align-middle cnic'},
                    {data: 'department', name: 'ad.name', class: 'align-middle department'},
                    {data: 'designation', name: 'admin_user_requests.designation', class: 'align-middle designtaion'},
                    {data: 'default_hub', name: 'c.name', class: 'align-middle default_hub'},
                    {data: 'request_craeted_by', name: 'a.name', class: 'align-middle request_craeted_by'},
                    {data: 'request_created_at', name: 'admin_user_requests.request_created_at', class: 'align-middle request_created_at'},
                    {data: 'verified_by_hr', name: 'a.name', class: 'align-middle verified_by_hr'},
                    {data: 'verified_by_hr_at', name: 'admin_user_requests.verified_by_hr_at', class: 'align-middle verified_by_hr_at'},
                    {data: 'launched_to_date', name: 'launched_to_date', class: 'align-middle launched_to_date', orderable: false, searchable: false},
                    {data: 'status', name: 'admin_user_requests.status ', class: 'align-middle status'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    // var info = table.page.info();

                    // $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Requested</option>' +
                        '<option value="1">Verified</option>' +
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
                    var link = '{{ route('admin.settings.user_requests.verify.index', ["id" => 0]) }}';
                    window.location = link.substr(0, link.lastIndexOf('/')) + '/' + id;
                }
            });
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                if ($(this).hasClass('addrole')) {
                    var link = '{{ route('admin.settings.user_requests.save.index', ["id" => 0]) }}';
                    window.location = link.substr(0, link.lastIndexOf('/')) + '/' + id;
                }
            });



            //bulk assigning of hub work start
           /* $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));
                console.log(id);
                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.assign').enable();
                }
                else {
                    table.button('.assign').disable();
                }
            });*/

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