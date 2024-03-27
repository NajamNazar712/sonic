@extends('admin.layout.master')

@section('title', 'CN Receive Admin Store')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    CN Receive Admin Store
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;width:100% !important;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">Company Code</th>
                                    <th class="border-primary border-darken-1">Area</th>
                                    <th class="border-primary border-darken-1">Product</th>
                                    <th class="border-primary border-darken-1">CN From</th>
                                    <th class="border-primary border-darken-1">To From</th>
                                    <th class="border-primary border-darken-1">Quantity</th>
                                    <th class="border-primary border-darken-1">Issue Date</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="AddCNReceiveAdminStoreModal" data-backdrop="static" role="dialog" aria-labelledby="AddCNReceiveAdminStoreModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add CN Receive Admin Store</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="errormessage">

                    </div>

                    <form method="post" id="add_cn_receive_admin_store_form"
                          action="{{ route('admin.logistic.cn.receive_admin_store.store') }}"
                          class="form-horizontal mb-1" novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Company Code</label>
                                    <input type="text" name="company_code" class="form-control" >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Issue Date</label>
                                    <input type="text" name="receive_date" class="form-control receive_date whitebackground" id="receive_date_datepicker" value="{{ Carbon\Carbon::today()->format('Y-m-d') }}" data-rule-required="true" data-msg-required="Pickup Date is Required">

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Area Code</label>
                                    <select class="select select2 mb-1" name="area_code" id="area_code_select" data-rule-required="true" data-msg-required="Area is required">
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Product</label>
                                    <select class="select select2 mb-1" name="product_id" id="product_id_select" data-rule-required="true" data-msg-required="Product is required">
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>CN From</label>
                                    <input type="text" name="cn_from" class="form-control" >
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>CN To</label>
                                    <input type="text" name="cn_to" class="form-control" >
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="text" name="quantity" class="form-control" >
                                </div>
                            </div>
                        </div>

                        <div class="form-group ml-1">

                            <button type="submit" name="add" class="btn btn-primary ml-2">Submit</button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>


                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <style>
        .btn-min-width {
            min-width: 5.5rem;
        }

        .legends {
            cursor: pointer;
        }

        .custom-nav {
            margin-left: 4px;
        }

        .custom-nav li {

            border: 1px solid #CCCCCC;
            border-radius: 4px;
        }

        .custom-nav li:first-child {

            margin-right: 4px !important;
        }

        .custom-nav li:last-child {

            margin-left: 4px !important;
        }

        .custom-nav .nav-item a.nav-link {

            color: #CCCCCC;
            border: 1px solid #CCCCCC !important;
        }

        .custom-nav .nav-item p {

            line-height: 1.4;
        }

        .custom-nav .nav-item a.active {

            /* color: #64A0D2 !important; */
            border: 1px solid #64A0D2 !important;
            /* background-color: #F7FAFC !important; */
            color: #fff!important;
            background: #5587b4!important;
        }

        .custom-nav .nav-item a:hover {
            color: #64A0D2 !important;
            border: 1px solid #64A0D2 !important;
            background-color: #F7FAFC !important;
        }

        /* start scheduled days area */

        /* Hide checkboxes */
        .scheduled_days_area input[type="checkbox"] {
            display: none;
        }

        /* Style labels for checkboxes */
        .scheduled_days_area input + label {
            display: inline-block;
            border: 1px solid #CCCCCC;
            background: #fff;
            padding: 5px 1px;
            color: #A3A3A3;
            border-radius: 5px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s;
        }

        /* Style the checkbox's unchecked state */
        .scheduled_days_area input:checked + label {
            background: #5587b4;
            border-color: #64A0D2;
            color: #fff;

        }

        /* Style the checkbox's unchecked state icon */
        .scheduled_days_area input:checked + label:before {
            font-size: 17px;
            position: absolute;
            left: 24px;
            top: 6px;
            opacity: 1;
        }

        /* .scheduled_days_area input + label:hover {
            background: #fff;
            border-color: #CCCCCC;
            color: #000;
        } */

        .scheduled_days_area input:not(:checked) + label:hover {
            background: #F7FAFC;
            border-color: #64A0D2;
            color: #64A0D2;
        }

        .scheduled_days_area .item-column {
            margin-right: 10px;
        }

        /* end scheduled days area */

        /* start addition services */

        .service-item {

            border: 1px solid #CCCCCC;
            border-radius: 4px;
            padding-top: 12px;
            padding-bottom: 12px;

        }

        .btn-service {
            border-radius: 50%;
            padding: 4px;
            width: 30px;
            height: 30px;
            transition: all 0.4s;
        }

        .btn-service:hover {

            background: #6496BE !important;
        }

        .btn-service:active,
        .btn-service:focus {

            background: #6496BE !important;
        }

        .service-item .custom-input-number[type="number"]::-webkit-inner-spin-button,
        .service-item .custom-input-number[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            appearance: none;
            margin: 0;
        }

        /* end addition services */
        .delay_time{
            background-color: #8fc5ea;
            /* background-color: #9fa1ae; */
            color: white;
            /* background-color: #FF0000; */
            /* background-color: #FFA500; */
        }
        .status_tab_active{
            color: #fff;
            background-color: #649bc8;
        }
        #add_cn_receive_admin_store_form label {
            float: left;
        }
        .whitebackground{
            background-color: #fff!important;
        }

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script>


        $(document).ready(function () {


          $('#receive_date_datepicker').pickadate({
                firstDay: 1,
                clear: '',
                min: '{{ Carbon\Carbon::today() }}',
                // format: 'dd mmmm, yyyy',
                format: 'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
            });

            $('#product_id_select').prepend('<option value="" selected="selected">Select Product</option>').select2({
                placeholder: 'Select Product',
                width: '100%',
                dropdownParent:$('#AddCNReceiveAdminStoreModal')
            });

            $('#area_code_select').prepend('<option value="" selected="selected">Select Area</option>').select2({
                placeholder: 'Select Area',
                width: '100%',
                dropdownParent:$('#AddCNReceiveAdminStoreModal')
            });



            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                @if (session('role_id') == 1 || count(array_intersect([934], session('permissions'))) !== 0)

                buttons: [
                    {
                        text: '<i class="la la-plus"></i> Add New',
                        className: 'btn btn-primary request_add',
                        action: function (e, dt, node, config) {
                            // $("#add_sack_bag_form")[0].reset();
                            // $("#sackbag_detail tr:not(:first-child)").empty();
                            // $("#add_sack_bag_form select").val(null).trigger('change.select2');
                            $('#AddCNReceiveAdminStoreModal').modal('show');

                        }
                    },

                    'reset'
                ],
                @else
                buttons: [
                    'reset'
                ],
                @endif
                scrollX: true, scrollY: '500px',
                // select: {
                //     info: false,
                //     style: 'multi',
                //     selector: 'td.select-checkbox',
                //     className: 'selected bg-primary bg-lighten-5 primary'
                // },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.logistic.cn.receive_admin_store.list') }}',

                },
                rowId: 'id',
                order: [[2, 'desc']],
                columns: [

                    {
                        data: 'serial_number',
                        orderable: false,
                        searchable: false,
                        name: 'id',
                        class: 'align-middle serial_number',
                        targets: 1,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'company_code', name: 'company_code', class: 'align-middle company_code'},
                    {data: 'area_name', name: 'c.name', class: 'align-middle area_name'},
                    {data: 'segment_name', name: 's.name', class: 'align-middle segment_name', orderable: false},
                    {data: 'cn_from', name: 'cn_from', class: 'align-middle cn_from'},
                    {data: 'cn_to', name: 'cn_to', class: 'align-middle cn_to'},
                    {data: 'quantity', name: 'quantity', class: 'align-middle quantity'},
                    {data: 'receive_date', name: 'receive_date', class: 'align-middle receive_date'},
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function () {

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();


                        // if ($(header).is('.action') || $(header).is('.select') || $(header).is('.serial_number') || $(header).is('.shipments') || $(header).is('.status') || $(header).is('.trax_reason') || $(header).is('.trax_remarks') || $(header).is('.shipper_remarks') || $(header).is('.attempted_date') || $(header).is('.action') || $(header).is('.rider_remarks') || $(header).is('.brand_name') || $(header).is('.all_remarks')) {
                        //     $(td).appendTo($(search));
                        // } else {
                        //     var current = $(input).appendTo($(search)).on('change', function () {
                        //         column.search($(this).val(), false, false, true).draw();
                        //     }).wrap(td).after(icon);

                        //     if (column.search()) {
                        //         current.val(column.search());
                        //     }
                        // }
                    });

                    this.api().table().columns.adjust();
                }



            });


            $("#add_cn_receive_admin_store_form").validate({
                errorClass: "danger",
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });


        });






    </script>
@endsection