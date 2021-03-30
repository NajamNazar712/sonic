@extends('admin.layout.master')

@section('title', 'Today\'s Active Accounts List')

@section('content')
    <h1>Active Accounts List</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')

                    <div class="card-content">
                        <div class="card-body card-dashboard">

                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3">
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No</th>
                                        <th class="border-primary border-darken-1">Account ID</th>
                                        <th class="border-primary border-darken-1">Account Type</th>
                                        <th class="border-primary border-darken-1">Company Name</th>
                                        <th class="border-primary border-darken-1">Contact Person</th>
                                        <th class="border-primary border-darken-1">Phone #</th>
                                        <th class="border-primary border-darken-1">Email</th>
                                        <th class="border-primary border-darken-1">Sales Person Tagged</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">


@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/datatable_buttons.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>


    <script type="text/javascript">
        $(document).ready(function() {

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function(options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    if (params !== undefined) {
                        params.start = 0;
                        params.length = -1;
                    }
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.accounts.active.ajax') }}',
                        method: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function(result) {
                            head = [];

                            head.push('S.No');
                            head.push('Account ID');
                            head.push('Account Type');
                            head.push('Company Name');
                            head.push('Contact Person');
                            head.push('Address');
                            head.push('City');
                            head.push('Territory');
                            head.push('Product Type');
                            head.push('Status');
                            head.push('Sales Person Tagged');
                            head.push('POC Tagged');
                            head.push('KAM Tagged');
                            head.push('REF Tagged');
                            head.push('Request Date');
                            head.push('Rates Added By');
                            head.push('Rates Updated By');
                            head.push('Rates Status');
                            head.push('Rates Status Remarks');
                            head.push('Rates Approved By');
                            head.push('Account Activated By');
                            head.push('Account Activation Date');
                            head.push('Account Disable Remarks');
                            head.push('Documents Uploaded At');
                            head.push('Documents Approved At');
                            head.push('Documents Status');
                            head.push('Documents Rejection Reason');
                            head.push('Intl Rates Status');
                            head.push('Intl Rates Status Remarks');
                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.id_padded);
                                row.push(values.account_type);
                                row.push(values.name);
                                row.push(values.poc);
                                row.push(values.address);
                                row.push(values.city);
                                row.push(values.territory);
                                row.push(values.product_type);
                                row.push(values.status);
                                row.push(values.admin_tag_id);
                                row.push(values.tagged_poc);
                                row.push(values.kam);
                                row.push(values.ref);
                                row.push(values.created_at);
                                row.push(values.added_by);
                                row.push(values.updated_by);
                                row.push(values.rate_status);
                                row.push(values.rejected_reason);
                                row.push(values.approved_by);
                                row.push(values.account_activated_by);
                                row.push(values.activated_date);
                                row.push(values.disable_remarks);
                                row.push(values.documents_uploaded_at);
                                row.push(values.documents_approved_at);
                                row.push(values.documents_status);
                                row.push(values.documents_rejection_reason);
                                row.push(values.international_rate_status);
                                row.push(values.international_rejected_reason);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {
                        body: body,
                        header: head
                    };
                }
            });
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true,
                scrollY: '800px',
                buttons: [

                    {
                        extend: 'excel',
                        title: 'Active Accounts',
                        className: 'btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],

                lengthMenu: [
                    [50, 100, 500, 1000, -1],
                    [50, 100, 500, 1000, 'All']
                ],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                deferLoading: 0,
               order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ route('admin.accounts.active.today.ajax') }}',
                    method: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    {orderable: false,searchable: false,name: 'serial_number',class: 'align-middle serial_number',targets: 0,render: function(data, type, row) {return '';}
                    },
                    {data: 'id_padded',name: 'users.id',class: 'align-middle account_id'},
                    {data: 'account_type',name: 'at.name',class: 'align-middle account_type'},
                    {data: 'name',name: 'name',class: 'align-middle company_name'},
                    {data: 'poc',name: 'poc',class: 'align-middle contact_person'},
                    {data: 'phone',name: 'phone',class: 'align-middle phone'},
                    {data: 'email',name: 'email',class: 'align-middle email'},
                    {data: 'admin_tag_id',name: 'ad.name',class: 'align-middle admin_tag_id'},
                ],
                rowCallback: function(row, data, index) {
                    //    var info = table.page.info();
                    //    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>')
                        .appendTo(this.api().table().header());

                    var td =
                        '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input =
                        '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon =
                        '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                  
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.action') || $(header).is(
                                '.serial_number') || $(header).is('.disable_remarks') || $(
                                header).is('.rate_status') || $(header).is('.duplicate') || $(
                                header).is('.international_rejected_reason')) {
                            $(td).appendTo($(search));
                        } else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    $("#documents_status_select").prepend('<option value="" selected></option>')
                        .select2({
                            placeholder: "Select a Status",
                            width: '100%',
                            containerCssClass: 'select-xs',
                            dropdownCssClass: 'form-control-sm p-0'
                        });
                    $("#intl_rate_status_select").prepend('<option value="" selected></option>')
                        .select2({
                            placeholder: "Select International Rate Status",
                            width: '100%',
                            containerCssClass: 'select-xs',
                            dropdownCssClass: 'form-control-sm p-0'
                        });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select a Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data1 = $.map({!! $products !!}, function(obj) {
                        obj.id = obj.id // replace pk with your identifier

                        return obj;
                    });
                    var data1 = $.map({!! $products !!}, function(obj) {
                        obj.text = obj
                        .product_name; // replace name with the property used for the text

                        return obj;
                    });

                    $("#product_select").prepend('<option value="" selected></option>').select2({
                        data: data1,
                        placeholder: "Select Product",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
        });

    </script>

@endsection
