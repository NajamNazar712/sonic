@extends('admin.layout.master')

@section('title', 'International Wholesale Accounts')

@section('content')
    <h1>International Wholesale Accounts</h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">

                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                            </div>
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-rule-required="true" data-msg-required="Date(From) is required">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                            </div>
                            <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-rule-required="true" data-msg-required="Date(To) is required">
                        </div>
                    </div>

                    <div class="col-2">
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-info btn-min-width"><i class="la la-search"></i> Search</button>
                        </div>
                    </div>
                </form>

                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                            <thead>
                            <tr class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No</th>
                                <th class="border-primary border-darken-1">Shipper ID</th>
                                <th class="border-primary border-darken-1">Shipper Name</th>
                                <th class="border-primary border-darken-1">Phone</th>
                                <th class="border-primary border-darken-1">Address</th>
                                <th class="border-primary border-darken-1">City</th>
                                <th class="border-primary border-darken-1">Email</th>
                                <th class="border-primary border-darken-1">Bank Name</th>
                                <th class="border-primary border-darken-1">Bank Account</th>
                                <th class="border-primary border-darken-1">NTN</th>
                                <th class="border-primary border-darken-1">Document View</th>
                                <th class="border-primary border-darken-1">Created By</th>
                                <th class="border-primary border-darken-1">Created At</th>
                                <th class="border-primary border-darken-1">Margin %</th>
                                <th class="border-primary border-darken-1">Updated By</th>
                                <th class="border-primary border-darken-1">Updated At</th>
                                <th class="border-primary border-darken-1">Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_shipper_modal" role="dialog" aria-labelledby="add_shipper_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Add Shipper</h3>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">

                    <form id="add_shipper_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate" method="POST" action="{{ route('admin.international.wholesale.accounts.store') }}" enctype="multipart/form-data">
                        @method('POST')
                        @csrf

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <input name="shipper_name" id="add_name" class="form-control select2" placeholder="Full Name*" data-rule-required="true"  data-msg-required="Full Name is required">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="phone_number" id="add_phone_number" placeholder="Phone Number*" data-rule-required="true"  data-msg-required="Phone Number is required">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="address" id="add_address" placeholder="Address*" data-rule-required="true"  data-msg-required="Address is required">
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <select name="city_id" id="add_city" class="form-control select2" data-rule-required="true"  data-msg-required="City is required">
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}"> {{ $city->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <input type="email" class="form-control" name="email_address" id="add_email" placeholder="Email*" data-rule-required="true"  data-msg-required="Email is required">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="ntn" id="add_ntn" placeholder="NTN*" data-rule-required="true"  data-msg-required="NTN is required">
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <select name="bank_id" id="add_bank" class="form-control select2" data-rule-required="true"  data-msg-required="Bank Name is required">
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank->id }}"> {{ $bank->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="bank_account" id="add_bank_account" placeholder="Bank Account*" data-rule-required="true"  data-msg-required="Bank Account is required">
                                </div>
                            </div>


                            <input type="hidden" name="selected_ids" id="selected_ids"/>
                            <div class="col-12">
                                <table class="table table-bordered datatable" id="documents_upload_table" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">

                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Image</th>
                                        <th class="border-primary border-darken-1"></th>

                                    </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <div class="form-group ml-1">
                                <button type="submit" class="btn btn-primary width-200" value="Add" id="AddShipperSubmitButton">Add</button>
                                <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            $("input[name='phone_number']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});



            $("#add_city").prepend('<option value="" selected></option>').select2({
                placeholder: "Select City",
                width: '100%'
            });
            $("#add_bank").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Bank",
                width: '100%'
            });
            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #to_date').pickadate('picker').set('min', $('#search_form #from_date').pickadate('picker').get('select'));
                    }
                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #from_date').pickadate('picker').set('max', $('#search_form #to_date').pickadate('picker').get('select'));
                    }
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
                        url: '{{ route('admin.reports.account_activation.list') }}',
                        data: params,
                        success: function (result) {

                            head = [];

                            head.push('S. No.');
                            head.push('Account No.');
                            head.push('Account Name');
                            head.push('Sales Person Name');
                            head.push('Activation Date');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.id);
                                row.push(values.shipper);
                                row.push(values.sale_person);
                                row.push(values.activated_at);
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
                // scrollX: true, scrollY: '500px',
                autoWidth:true,
                buttons: [
                    @if (session('role_id') == 1 || in_array(769, session('permissions')))
                    {
                        text: '<i class="la la-plus"></i> Add Shipper',
                        className: 'btn btn-primary add_shipper',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#add_shipper_modal').modal('show');
                        }
                    },
                    @endif
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Account Activation Report',
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
                deferLoading: [50, 0],
                ajax:{
                    url: '{{ route('admin.international.wholesale.accounts.list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="from_date_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                order: [[12, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'shipper_id_padded' ,name: 'wholesale_users.id', class: 'align-middle text-center shipper_id'},
                    { data:'shipper_name' ,name: 'wholesale_users.name', class: 'align-middle text-center shipper_name'},
                    { data:'phone' ,name: 'wholesale_users.phone', class: 'align-middle text-center phone'},
                    { data:'address' ,name: 'wholesale_users.address', class: 'align-middle text-center address'},
                    { data:'city' ,name: 'c.name', class: 'align-middle text-center city'},
                    { data:'email' ,name: 'wholesale_users.email', class: 'align-middle text-center email'},
                    { data:'bank_name' ,name: 'wholesale_users.bank_name', class: 'align-middle text-center bank_name'},
                    { data:'bank_account' ,name: 'wholesale_users.bank_account', class: 'align-middle text-center bank_account'},
                    { data:'ntn' ,name: 'wholesale_users.ntn', class: 'align-middle text-center ntn'},
                    { data:'document' ,name: 'document', class: 'align-middle text-center document', orderable: false, searchable: false},
                    { data:'created_by' ,name: 'cb.name', class: 'align-middle text-center created_by'},
                    { data:'created_at' ,name: 'wholesale_users.created_at', class: 'align-middle text-center created_at'},
                    { data:'margin_percentage' ,name: 'wholesale_users.margin', class: 'align-middle text-center margin_percentage'},
                    { data:'updated_by' ,name: 'ub.name', class: 'align-middle text-center updated_by'},
                    { data:'updated_at' ,name: 'wholesale_users.updated_at', class: 'align-middle text-center updated_at'},
                    {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {

                    table.draw(true);
                }
            });






            $.validator.addMethod('maxsize', function(value, element, params) {
                if ($(element).attr('type') === 'file') {
                    if (element.files && element.files.length) {
                        for (var c = 0; c < element.files.length; c++) {
                            if (element.files[c].size > params) {
                                return false;
                            }
                        }
                    }
                }

                return true;
            }, $.validator.format("File Size must not exceed {0} bytes."));
            var rows_count = 0;
            var selected_rows = [];
            var documents_image_table;
            function add_row() {
                rows_count++;

                var document = '<input class="form-control form-control-sm" type="file" name="document_'+rows_count+'" data-rule-extension="jpeg|jpg|png|docx|pdf|doc" data-msg-extension="Only file with extension jpeg, jpg , png, pdf, doc, docx allowed" data-rule-accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only file with extension jpeg, jpg , png, pdf, doc, docx allowed" data-rule-maxsize="4097152" data-msg-maxsize="File Size must not exceed 4 MB (4098 KB)." data-rule-required="true" data-msg-required="Document is required">';

                if(rows_count == 1){
                    var remove = '';
                }else{
                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';

                }
                selected_rows.push(rows_count);
                documents_image_table.row.add([0, document,remove]).node().id = rows_count;
                documents_image_table.draw(true);

                $('#AddShipperSubmitButton').attr('disabled', false);


            }


            documents_image_table = $('#documents_upload_table').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[{
                    title: 'Add Row',
                    className: 'btn btn-primary mb-1',
                    text: '<i class="la la-plus"></i> Add Row',
                    action:function (e) {
                        add_row();
                    }
                }],
                ordering:false,
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {name: 'image', class: 'align-middle image form-group'},
                    {name: 'action', class: 'align-middle action'},
                ],

                rowCallback: function(row, data, index) {
                    var info = documents_image_table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },

            });

            $('body').on('click', 'a.remove_row',function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, selected_rows);

                if (index !== -1) {
                    selected_rows.splice(index, 1);
                }
                documents_image_table.row( $(this).parents('tr') ).remove().draw();
            });

            $('#add_shipper_modal').on('hidden.bs.modal', function () {

                rows_count = 0;
                selected_rows = [];
                documents_image_table.clear().draw();
                $('#add_shipper_form').validate().resetForm();
                $('#AddShipperSubmitButton').attr('disabled', true);
            });

            $('#add_shipper_form').validate({

                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    $('#selected_ids').val(selected_rows);
                    swal({
                        title: 'Please Wait!',
                        text: 'Shipper is being Added!',
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

