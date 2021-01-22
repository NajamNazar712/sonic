@extends('admin.layout.master')

@section('title', 'Pending Accounts List')

@section('content')
    <h1>Pending Accounts List</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">

                        @include('admin.inc.messages')

                    </div>
                    @if (session('role_id') == 1 || count(array_intersect([276, 321], session('permissions'))) !== 0)
                        <div id="search_form" class="row p-1 mb-2 justify-content-center">
                            <div class="col-3" >
                               <input name="fuel_surcharge" id="fuel_surcharge" placeholder="Fuel Surcharge">
                            </div>

                            <div class="col-3">
                                <input name="exchange_rate" id="fuel_surcharge" placeholder="Exchange Rate">
                            </div>
                            <div class="col-3">
                                <input name="margin" id="margin" placeholder="Margin">
                            </div>
                            <div class="col-3">
                                <input name="gst" id="gst" placeholder="GST">
                            </div>
{{--                            <div class="col-2">--}}
{{--                                <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>--}}
{{--                            </div>--}}
                        </div>
                    @endif

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">

                                    <th class="border-primary border-darken-1">KG </th>
                                    <th class="border-primary border-darken-1">Zone 1 </th>
                                    <th class="border-primary border-darken-1">Zone 2 </th>
                                    <th class="border-primary border-darken-1">Zone 3 </th>
                                    <th class="border-primary border-darken-1">Zone 4 </th>
                                    <th class="border-primary border-darken-1">Zone 5 </th>
                                    <th class="border-primary border-darken-1">Zone 6 </th>
                                    <th class="border-primary border-darken-1">Zone 7 </th>
                                    <th class="border-primary border-darken-1">Zone 8 </th>
                                    <th class="border-primary border-darken-1">Zone 9 </th>
                                    <th class="border-primary border-darken-1">Zone 10 </th>
                                    <th class="border-primary border-darken-1">Zone 11 </th>

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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.accounts.pending.ajax') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Account ID');
                            head.push('Account Type');
                            head.push('Company Name');
                            head.push('Contact Person');
                            head.push('City');
                            head.push('Product Type');
                            head.push('Request Date');
                            head.push('Status');
                            head.push('Sales Person Tagged');
                            head.push('POC Tagged');
                            head.push('KAM Tagged');
                            head.push('REF Tagged');
                            head.push('Rate Status');
                            head.push('Rates Status Remarks');
                            head.push('Rates Added By');
                            head.push('Rates Approved By');
                            head.push('Documents Uploaded At');
                            head.push('Documents Approved At');
                            head.push('Documents Status');
                            head.push('Documents Rejection Reason');
                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.id_padded);
                                row.push(values.account_type);
                                row.push(values.name);
                                row.push(values.poc);
                                row.push(values.city);
                                row.push(values.product_type);
                                row.push(values.created_at);
                                row.push(values.status);
                                row.push(values.admin_tag_id);
                                row.push(values.tagged_poc);
                                row.push(values.kam);
                                row.push(values.ref);
                                row.push(values.rate_status);
                                row.push(values.rejected_reason);
                                row.push(values.rates_added_by);
                                row.push(values.rates_authorized_by);
                                row.push(values.documents_uploaded_at);
                                row.push(values.documents_approved_at);
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
                        extend: 'excel',
                        title: 'Pending Accounts',
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
                    url: '{{ route('admin.accounts.pending.ajax') }}',
                    data: function (d) {
                        d.sale_persons = $('#search_admins').val();
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
                    {data: 'poc', name: 'poc', class: 'align-middle contact_person'},
                    {data: 'city', name: 'cities.name', class: 'align-middle city'},
                    {data: 'product_type', name: 'product_type', class: 'align-middle product_type'},
                    {data: 'created_at', name: 'created_at', class: 'align-middle created'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'admin_tag_id', name: 'ad.name', class: 'align-middle admin_tag_id'},
                    {data: 'tagged_poc', name: 'p.name', class: 'align-middle tagged_poc'},
                    {data: 'kam', name: 'k.name', class: 'align-middle kam'},
                    {data: 'ref', name: 'r.name', class: 'align-middle ref'},
                    {data: 'rate_status', name: 'users.rate_status', class: 'align-middle rate_status'},
                    {data: 'rejected_reason', name: 'users.rejected_reason', class: 'align-middle rejected_reason'},
                    {data: 'rates_added_by', name: 'rab.name', class: 'align-middle rates_added_by'},
                    {data: 'rates_authorized_by', name: 'rabb.name', class: 'align-middle rates_authorized_by'},
                    {data: 'documents_uploaded_at', name: 'uda.uploaded_at', class: 'align-middle documents_uploaded_at'},
                    {data: 'documents_approved_at', name: 'uda.approved_at', class: 'align-middle documents_approved_at'},
                    {data: 'documents_status', name: 'users.documents_status', class: 'align-middle documents_status'},
                    {data: 'documents_rejection_reason', name: 'users.documents_status_reason', class: 'align-middle documents_rejection_reason'},
                    {data: 'duplication', name: 'duplication', class: 'align-middle duplicate', orderable: false, searchable: false},
                    {data: 'international_rate_status', name: 'international_rate_status', class: 'align-middle international_rate_status', orderable: false, searchable: false},
                    {data: 'international_rejected_reason', name: 'international_rejected_reason', class: 'align-middle international_rejected_reason', orderable: false, searchable: false},
                    {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }

                    //    var info = table.page.info();
                    //    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Request Received</option>' +
                        '<option value="1">Rates Added</option>' +
                        '<option value="2">Pending For Activation</option>' +
                        '<option value="5">Rates Rejected</option>' +
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

                        if ($(header).is('.select') || $(header).is('.action') || $(header).is('.serial_number') || $(header).is('.rate_status') || $(header).is('.duplicate') || $(header).is('.international_rate_status') || $(header).is('.international_rejected_reason')) {
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

                    this.api().table().columns.adjust();
                }
            });
            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

        });

    </script>

@endsection

