@extends('admin.layout.master')

@section('title', 'Active Accounts List')

@section('content')
    <h1>Active Accounts List</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @if (session('role_id') == 1 || count(array_intersect([276, 321], session('permissions'))) !== 0)
                            <div id="search_form" class="row p-1 mb-2 justify-content-center">
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <select name="search_admins[]" id="search_admins" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                            @foreach($sale_name as $admin)
                                                <option value="{{$admin->id}}">{{$admin->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <input type="text" name="search_phone" id="search_phone" class="form-control phone" placeholder="Phone Number">
                                    </fieldset>
                                </div>
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <input type="text" name="search_iban" id="search_iban" class="form-control iban" placeholder="IBAN">
                                    </fieldset>
                                </div>
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <input type="text" name="search_cnic" id="search_cnic" class="form-control cnic" placeholder="CNIC">
                                    </fieldset>
                                </div>
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <input type="text" name="search_shipper" id="search_shipper" class="form-control shipper_name" placeholder="Shipper Name">
                                        <select name="search_shipper[]" id="search_shipper" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                           @foreach($shippers as $shipper)
                                                <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-2">
                                    <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>
                            @endif
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3">
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1"></th>
                                        <th class="border-primary border-darken-1">S. No</th>
                                        <th class="border-primary border-darken-1">Account ID</th>
                                        <th class="border-primary border-darken-1">Account Type</th>
                                        <th class="border-primary border-darken-1">Company Name</th>
                                        <th class="border-primary border-darken-1">City Name</th>
                                        <th class="border-primary border-darken-1">Contact Person</th>
                                        <th class="border-primary border-darken-1">Phone Number</th>
                                        <th class="border-primary border-darken-1">Address</th>
                                        <th class="border-primary border-darken-1">Email Address</th>
                                        <th class="border-primary border-darken-1">Product Type</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1">Sales Person Tagged</th>
                                        <th class="border-primary border-darken-1">Request Date</th>
                                        <th class="border-primary border-darken-1">Rate Added By</th>
                                        <th class="border-primary border-darken-1">Rate Updated By</th>
                                        <th class="border-primary border-darken-1">Rate Status</th>
                                        <th class="border-primary border-darken-1">Rate Status Remarks</th>
                                        <th class="border-primary border-darken-1">Rate Approved By</th>
                                        <th class="border-primary border-darken-1">Account Activated By</th>
                                        <th class="border-primary border-darken-1">Account Activation Date</th>
                                        <th class="border-primary border-darken-1">Account Disable Remarks</th>
                                        <th class="border-primary border-darken-1">Documents Status</th>
                                        <th class="border-primary border-darken-1">Documents Rejection Reason</th>
                                        <th class="border-primary border-darken-1">Duplicate</th>
                                        <th class="border-primary border-darken-1">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade text-left" id="SalesTagModal1" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SalesTagModal1"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag Sales Person</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="shipper_id1">
                    <select name="Sale_person" id="saletag1" class="form-control select2">
                        @foreach($sale_name as $sn)
                            <option value="{{ $sn->id }}" > {{ $sn->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="salesTagSubmit1">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="SalesTagModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SalesTagModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag Sales Person</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="shipper_id">
                    <select name="Sale_person" id="saletag" class="form-control select2">
                        @foreach($sale_name as $sn)
                            <option value="{{ $sn->id }}" > {{ $sn->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="salesTagSubmit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="ShipmentCancellationDaysModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ShipmentCancellationDaysModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Auto Shipment Cancel Days</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="shipper_id">
                    <input type="text" class="form-control cancellation_days" name="cancellation_days" id="cancellation_days" placeholder="Days*" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="accountDisableDaysSubmit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="duplicate_modal" data-backdrop="static" role="dialog" aria-labelledby="duplicate_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="bookings_modal_title">Duplicate Data</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">



@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
    $(document).ready(function() {
        $("input[name='search_phone']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
        $("input[name='search_cnic']").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});
        $('body').on('change','#search_iban',function() {
            $(this).val($(this).val().trim());
        });
        $('.cancellation_days').inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'min': 0,
            'max': 200
        });

        $('#search_admins').select2({
            width:'100%',
            placeholder:"Select Sale Persons",
            allowClear:true,
        });
        $('#search_shipper').select2({
            width:'100%',
            placeholder:"Select Shipper",
            allowClear:true,
         });
        jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            if ( this.context.length ) {
                body = [];
                var params = table.ajax.params();
                params.start = 0;
                params.length = -1;
                var jsonResult = $.ajax({
                    url: '{{ route('admin.accounts.active.ajax') }}',
                    data: params,
                    success: function (result) {
                        head = [];

                        head.push('S.No');
                        head.push('Account ID');
                        head.push('Account Type');
                        head.push('Company Name');
                        head.push('City Name');
                        head.push('Contact Person');
                        head.push('Phone No.');
                        head.push('Company Address');
                        head.push('Email Address');
                        head.push('Product Type');
                        head.push('Status');
                        head.push('Sales Person Tagged');
                        head.push('Request Date');
                        head.push('Rates Added By');
                        head.push('Rates Updated By');
                        head.push('Rates Status');
                        head.push('Rates Status Remarks');
                        head.push('Rates Approved By');
                        head.push('Account Activated By');
                        head.push('Account Activation Date');
                        head.push('Account Disable Remarks');
                        head.push('Documents Status');
                        head.push('Documents Rejection Reason');
                        $.each(result.data, function(index, values) {
                            row = [];


                            row.push(index + 1);
                            row.push(values.id_padded);
                            row.push(values.account_type);
                            row.push(values.name);
                            row.push(values.city);
                            row.push(values.poc);
                            row.push(values.shipper_phone);
                            row.push(values.address);
                            row.push(values.email);
                            row.push(values.product_type);
                            row.push(values.status);
                            row.push(values.admin_tag_id);
                            row.push(values.created_at);
                            row.push(values.added_by);
                            row.push(values.updated_by);
                            row.push(values.rate_status);
                            row.push(values.rejected_reason);
                            row.push(values.approved_by);
                            row.push(values.account_activated_by);
                            row.push(values.activated_date);
                            row.push(values.disable_remarks);
                            row.push(values.documents_status);
                            row.push(values.documents_rejection_reason);

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
                        text: 'Set Commission',
                        className: 'btn btn-primary set_commission',
                        enabled:false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to Set Commission!',
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
                                        var link = '{{ route('admin.settings.commission.set_commission', ["ids" => 0]) }}';
                                        window.location = link.substr(0, link.lastIndexOf('/')) + '/' + selected_rows;
                                    }
                                });


                            }
                            else{
                                var error = "Something went wrong please refresh page and try again!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                        }
                    },
                    {
                        text: 'Approve Commission',
                        className: 'btn btn-primary approve_commission',
                        enabled:false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to Approve Commission!',
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
                                        var link = '{{ route('admin.settings.commission.approve_commission', ["ids" => 0]) }}';
                                        window.location = link.substr(0, link.lastIndexOf('/')) + '/' + selected_rows;
                                    }
                                });


                            }
                            else{
                                var error = "Something went wrong please refresh page and try again!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                        }
                    },
                    @if (session('role_id') == 1 || in_array(361, session('permissions')))
                    {
                            text: 'Bulk Tagging',
                            className: 'btn btn-primary bulk_tagging',
                            enabled:false,
                            action: function (e, dt, node, config) {
                           if(selected_rows != ''){
                              
                                $('#SalesTagModal1').modal('show');
                                // console.log(selected_rows);
                                $('#salesTagSubmit1').on('click',function () {
                                    var assign = parseInt($('#saletag1').val());
                                    swal({
                                        text: 'Are you sure, you want to Tag?',
                                        icon: 'info',
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
                                    }).then(function(confirm) {
                                        if (confirm) {
                                            if (assign) {
                                                $.ajax({
                                                    url: '{!! route('admin.accounts.tag.submit.bulk') !!}',
                                                    method: 'POST',
                                                    data: {
                                                        'admin_id': assign,
                                                        'shipper_ids[]': selected_rows,
                                                        '_token': '{{ csrf_token() }}'
                                                    }
                                                })
                                                    .done(function (data) {
                                                        if (data.status == 1) {
                                                            $('#SalesTagModal1').modal('hide');
                                                            toastr.success(data.success, 'Success!', {
                                                                positionClass: 'toast-bottom-center',
                                                                containerId: 'toast-bottom-center'
                                                            });
                                                        } else {
                                                            toastr.error(data.error, 'Error!', {
                                                                positionClass: 'toast-top-center',
                                                                containerId: 'toast-top-center'
                                                            });
                                                        }
                                                        selected_rows = [];

                                                        table.rows().deselect();
                                                        $('#saletag1').val('').trigger('change');
                                                        $('#SalesTagModal1').modal('hide');
                                                        table.draw(true);
                                                        table.button('.bulk_tagging').disable();
                                                        table.button('.set_commission').disable();
                                                        table.button('.approve_commission').disable();

                                                    });
                                            } else {
                                                var error = "Account Not Selected!";
                                                toastr.error(error, 'Error!', {
                                                    positionClass: 'toast-top-center',
                                                    containerId: 'toast-top-center'
                                                });
                                            }
                                        }
                                    });
                                });

                            }else{
                                var error = "Account Not selected!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }
                    },
                    @endif
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());

                                    hub_id = $(row.node()).data('id');

                                    var allow = false;

                                    if(hub_ids.length == 0) {
                                        hub_ids.push(hub_id);

                                        allow = true;
                                    }
                                    else if(hub_ids[0] == hub_id) {
                                        allow = true;
                                    }

                                    if (allow) {
                                        row.select();

                                        var index = $.inArray(id, selected_rows);

                                        if (index === -1) {
                                            selected_rows.push(id);
                                        }

                                        table.button('.bulk_tagging').enable();
                                        table.button('.set_commission').enable();
                                        table.button('.approve_commission').enable();
                                    }
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

                                if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.bulk_tagging').disable();
                                        table.button('.set_commission').disable();
                                        table.button('.approve_commission').disable();
                                        hub_ids.splice(index, 1);
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Active Accounts',
                        className:'btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
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
            rowId: 'id',
            order: [[2, 'desc']],
            ajax: {
               url: '{{ route('admin.accounts.active.ajax') }}',
               data: function (d) {
                   d.sale_persons = $('#search_admins').val();
                   d.search_phone = $('#search_phone').val();
                   d.search_cnic = $('#search_cnic').val();
                   d.search_shipper = $('#search_shipper').val();
                   d.search_iban = $('#search_iban').val();
               }
           },
            columns: [
                {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'id_padded', name: 'users.id', class: 'align-middle account_id'},
                {data: 'account_type', name: 'at.name', class: 'align-middle account_type'},
                {data: 'name', name: 'name', class: 'align-middle company_name'},
                {data: 'city', name: 'cities.name', class: 'align-middle city'},
                {data: 'poc', name: 'poc', class: 'align-middle contact_person'},
                {data: 'shipper_phone', name: 'shipper_phone', class: 'align-middle phone'},
                {data: 'address', name: 'address', class: 'align-middle address'},
                {data: 'email', name: 'email', class: 'align-middle email'},
                {data: 'product_type', name: 'product_type', class: 'align-middle product_type'},
                {data: 'status', name: 'status', class: 'align-middle status'},
                {data: 'admin_tag_id', name: 'ad.name', class: 'align-middle admin_tag_id'},
                {data: 'created_at', name: 'users.created_at', class: 'align-middle created_at'},
                {data: 'added_by', name: 'rab.name', class: 'align-middle added_by'},
                {data: 'updated_by', name: 'rabna.name', class: 'align-middle updated_by'},
                {data: 'rate_status', name: 'rate_status', class: 'align-middle rate_status'},
                {data: 'rejected_reason', name: 'users.rejected_reason', class: 'align-middle rejected_reason'},
                {data: 'approved_by', name: 'rabb.name', class: 'align-middle approved_by'},
                {data: 'account_activated_by', name: 'rabba.name', class: 'align-middle account_activated_by'},
                {data: 'activated_date', name: 'users.activated_at', class: 'align-middle activated_date'},
                {data: 'disable_remarks', name: 'users.disable_remarks', class: 'align-middle disable_remarks', orderable: false, searchable: false},
                {data: 'documents_status', name: 'users.documents_status', class: 'align-middle documents_status'},
                {data: 'documents_rejection_reason', name: 'users.documents_status_reason', class: 'align-middle documents_rejection_reason'},
                {data: 'duplication', name: 'duplication', class: 'align-middle duplicate', orderable: false, searchable: false},
                {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false}
            ],
           rowCallback: function(row, data, index) {
            //    var info = table.page.info();
            //    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
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
                var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                    '<option value="3">Enable</option>' +
                    '<option value="4">Disable</option>' +
                    '</select>';
                var documents_drop_select = '<select name="documents_status_select" id="documents_status_select" class="select2 form-control">' +
                    '<option value="0">Incomplete</option>' +
                    '<option value="1">Pending for Approval</option>' +
                    '<option value="2">Approved</option>' +
                    '<option value="3">Rejected</option>' +
                    '</select>';
                var product_select = '<select name="product_select" id="product_select" class="select2 form-control"></select>';

                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();

                    if ($(header).is('.select') || $(header).is('.action')  || $(header).is('.serial_number') || $(header).is('.disable_remarks') || $(header).is('.rate_status') || $(header).is('.duplicate') ) {
                        $(td).appendTo($(search));
                    }else if($(header).is('.status')){
                        $(drop_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }else if($(header).is('.product_type')){
                        $(product_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }else if($(header).is('.documents_status')){
                        $(documents_drop_select).appendTo($(search))
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
                $("#documents_status_select").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select a Status",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                $("#status_select").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select a Status",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                var data1 = $.map({!! $products !!}, function (obj) {
                    obj.id = obj.id // replace pk with your identifier

                    return obj;
                });
                var data1 = $.map({!! $products !!}, function (obj) {
                    obj.text = obj.product_name; // replace name with the property used for the text

                    return obj;
                });

                $("#product_select").prepend('<option value="" selected></option>').select2({
                    data:data1,
                    placeholder: "Select Product",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                this.api().table().columns.adjust();
            }
        });

        var hub_ids = [];

        $('#search_filter_btn').on('click',function () {
            table.draw();
        });
        $('body').on('change','.blacklist_reason',function() {
            $(this).val($(this).val().trim());
        });
        $('body').on('click','button.blacklist',function () {
            var id = $(this).parents('tr').attr('id');
            var status = $(this).attr('rel');
            swal({
                // title: 'Are You Sure?',
                text: 'Write a reason to blacklist this account!',
                content: {
                    element: "input",
                    attributes: {
                        placeholder: "Write a reason",
                        class: "form-control blacklist_reason",
                    },
                },
                buttons: {
                    cancel: {
                        text: 'No',
                        value: false,
                        visible: true,
                        closeModal: true,
                    },
                    confirm: {
                        text: 'Yes',
                        value: true,
                        visible: true,
                        closeModal: false
                    }
                },
                closeOnClickOutside: false,
                closeOnEsc: false,
                dangerMode: true
            }).then((value) => {
                    if (value) {
                        if (value === '') {
                            swal("You have not selected any reason!", {
                                icon: "warning",
                            });
                        } else {
                        if (id) {
                            $.ajax({
                                url: '{!! route('admin.accounts.status.block') !!}',
                                method: 'POST',
                                data: {
                                    'id': id,
                                    'reason': value,
                                    'status': status,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                swal.close();
                                if (data.status === 1) {
                                    table.draw('false');
                                    swal.close();
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }

                            });
                        }
                    }
                    }else{
                        swal.close();
                    }

            });


        });
        $("#saletag").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Sales Person",
            width:'100%',
            dropdownParent:$('#SalesTagModal')
        });
        $("#saletag1").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Sales Person",
            width:'100%',
            dropdownParent:$('#SalesTagModal1')
        });
        $('#SalesTagModal').on('shown.bs.modal',function (e) {
            var $invoker = $(e.relatedTarget);
            var shipper_id = $invoker.data('target-id');
            $('#shipper_id').val(shipper_id);
        });
        $('#salesTagSubmit').on('click',function () {
            var shipper = $('#shipper_id').val();
            var tag = parseInt($('#saletag').val());
            if(tag){
                $.ajax({
                    url: '{!! route('admin.accounts.tag.submit') !!}',
                    method: 'POST',
                    data: {
                        'admin_id': tag,
                        'shipper_id':shipper,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        if(data.status){
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }
                        else {
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        $('#saletag').val('').trigger('change');
                        $('#SalesTagModal').modal('hide');
                        table.draw(true);
                    });
            }else{
                var error = "Sales Person Not Selected!";
                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            }

        });
        $('body').on('click','button.userenable',function () {
            var status  = "enable";
            var id = $(this).parents('tr').attr('id');
            swal({
                title: 'Are You Sure?',
                text: 'Select Yes to enable this account!',
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
                    if(id){
                        $.ajax({
                            url: '{!! route('admin.accounts.status.change') !!}',
                            method: 'POST',
                            data: {
                                'id':id,
                                'status':status,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status === 1){
                                table.draw('false');
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }

                        });
                    }
                }
            });

        });
        $('body').on('click','button.userdisable',function () {
            var status  = "disable";
            var id = $(this).parents('tr').attr('id');
            swal({
                title: 'Are You Sure?',
                text: 'Select Yes to Disable this account!',
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
                   if(id){
                       $.ajax({
                           url: '{!! route('admin.accounts.status.change') !!}',
                           method: 'POST',
                           data: {
                               'id':id,
                               'status':status,
                               '_token': '{{ csrf_token() }}'
                           }
                       }).done(function (data) {
                           if(data.status == 1){
                               table.draw('false');
                               toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                           }else{
                               toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                           }

                       });
                   }
               }
            });

        });

        $('#datatable').on('click', 'button.warehousing_enable', function(){
            var id = $(this).parents('tr').attr('id');
            if(id){
               $.ajax({
                   url: '{!! route('admin.accounts.warehousing.active') !!}',
                   method: 'POST',
                   data: {
                       'id':id,
                       '_token': '{{ csrf_token() }}'
                   }
               }).done(function (data) {
                   if(data.status == 1){
                       table.draw('false');
                       toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                   }else{
                       toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                   }

               });
            }
        });

        $('#datatable').on('click', 'button.warehousing_disable', function(){
            var id = $(this).parents('tr').attr('id');
            if(id){
               $.ajax({
                   url: '{!! route('admin.accounts.warehousing.inactive') !!}',
                   method: 'POST',
                   data: {
                       'id':id,
                       '_token': '{{ csrf_token() }}'
                   }
               }).done(function (data) {
                   if(data.status == 1){
                       table.draw('false');
                       toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                   }else{
                       toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                   }

               });
            }
        });

        $('body').on('click','button.shipment_days_button',function () {
            var id = $(this).parents('tr').attr('id');
            var days = table.row($(this).parents('tr')).data().auto_shipment_cancellation_days;
            $('#shipper_id').val(id);
            $('#cancellation_days').val(days);
            $('#ShipmentCancellationDaysModal').modal('show');
        });

        $('#accountDisableDaysSubmit').on('click',function () {
            var shipper = $('#shipper_id').val();
            var days = parseInt($('#cancellation_days').val());
            if(days){
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to update auto disable days for this account!',
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
                        $.ajax({
                            url: '{!! route('admin.accounts.auto_shipment_cancel_days.submit') !!}',
                            method: 'POST',
                            data: {
                                'days': days,
                                'shipper_id':shipper,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function(data) {
                                if(data.status == 1){
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                                else {
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                                $('#ShipmentCancellationDaysModal').modal('hide');
                                table.draw(true);
                            });
                    }
                });
            }else{
                var error = "Days field is required!";
                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            }

        });

        $('body').on('click', 'button.duplicate_modal',  function(){
            var id = $(this).parents('tr').attr('id');
            if(id){
                $.ajax({
                    url: '{!! route('admin.accounts.duplicate.info') !!}',
                    data: {
                        'shipper_id': id,
                    }
                })
                    .done(function(data) {
                        if(data.status){
                            $('#duplicate_modal').modal('show');
                            var html = '<table class="table table-bordered"><tr><td><strong>Phone</strong></td><td>'+ data.info.phone +'</td></tr><tr><td><strong>CNIC</strong></td><td>'+ data.info.cnic +'</td></tr><tr><td><strong>IBAN</strong></td><td>'+ data.info.iban +'</td></tr><tr><td><strong>Name</strong></td><td>'+ data.info.name +'</td></tr>';
                            $('#duplicate_modal .modal-body').html(html);
                        }

                    });
            }
        });

        $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
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
                    table.button('.bulk_tagging').enable();
                    table.button('.set_commission').enable();
                    table.button('.approve_commission').enable();

                }
                else {
                    table.button('.bulk_tagging').disable();
                    table.button('.set_commission').disable();
                    table.button('.approve_commission').disable();
                }
        });
    });

</script>

@endsection

