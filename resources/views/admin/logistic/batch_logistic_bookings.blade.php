@extends('admin.layout.master')

@section('title', 'Batch Logistic Booking')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Batch Logistic Booking
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;width:100% !important;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">Booking Date</th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Pickup Address</th>
                                    <th class="border-primary border-darken-1">Product</th>
                                    <th class="border-primary border-darken-1">Service</th>
                                    <th class="border-primary border-darken-1">Rider</th>
                                    <th class="border-primary border-darken-1">Booking Weight</th>
                                    <th class="border-primary border-darken-1">Total Pieces</th>s
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Consignee Name</th>
                                    <th class="border-primary border-darken-1">Consignee Address</th>
                                    <th class="border-primary border-darken-1">Action</th>

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
        #add_cn_area_store_form label {
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



            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                @if (session('role_id') == 1 || count(array_intersect([934], session('permissions'))) !== 0)

                buttons: [
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
                    url: '{{ route('admin.logistic.batch.batch_booking_list') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN':'{{ csrf_token() }}'
                    },
                    data: {
                        batch_id: '{{ request('batch_id') }}'
                    },

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
                    {data: 'booking_date', name: 'booking_date', class: 'align-middle booking_date'},
                    {data: 'cn_number', name: 'cn_number', class: 'align-middle cn_number'},
                    // {data: 'shipper_name', name: 'u.name', class: 'align-middle shipshipper_trax_idper_name',render: function (data, type, row){
                    //     return row.shipper_trax_id +'-'+ row.shipper_name;
                    // }},
                    {data: 'shipper_name', name: 'u.name', class: 'align-middle shipper_name'},
                    {data: 'pickup_address', name: 'pickup_address', class: 'align-middle pickup_address'},
                    {data: 'product_name', name: 'p.product_name', class: 'align-middle product_name'},
                    {data: 'service_name', name: 's.service_name', class: 'align-middle service_name'},
                    {data: 'rider_name', name: 'r.name', class: 'align-middle rider_name'},
                    {data: 'total_booking_weight', name: 'total_booking_weight', class: 'align-middle total_booking_weight'},
                    {data: 'total_pieces', name: 'total_pieces', class: 'align-middle total_pieces'},
                    {data: 'origin_name', name: 'oc.name', class: 'align-middle origin_name'},
                    {data: 'destination_name', name: 'dc.name', class: 'align-middle destination_name'},
                    {data: 'consignee_name', name: 'consignee_name', class: 'align-middle consignee_name'},
                    {data: 'consignee_address', name: 'consignee_address', class: 'align-middle consignee_address'},
                    {data: 'action', name: 'action', class: 'align-middle action'}

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

            //get booking for edit
            {{--$("body").on('click','.datatable .edit',function (){--}}
            {{--    var id = parseInt($(this).closest('tr').attr('id'));--}}
            {{--    --}}
            {{--    --}}{{--$.ajax({--}}
            {{--    --}}{{--    url:'{{route('admin.logistic.master_product.edit',['id'=>':id']) }}'.replace(':id',id),--}}
            {{--    --}}{{--    method:'GET'--}}
            {{--    --}}{{--}).done(function (data){--}}
            {{--    --}}{{--    if(data.status==0)--}}
            {{--    --}}{{--    {--}}
            {{--    --}}{{--        var master_product = data.master_product;--}}
            {{--    --}}{{--        $("#id").val(master_product.id);--}}
            {{--    --}}{{--        $("#edit_parent_code").val(master_product.parent_code);--}}
            {{--    --}}{{--        $("#edit_parent_name").val(master_product.parent_name);--}}
            {{--    --}}{{--        $("#edit_segment_id_select").val(master_product.segment_id).trigger('change');--}}
            {{--    --}}{{--        $("#EditMasterProductModal").modal("show");--}}
            {{--    --}}
            {{--    --}}{{--    } else {--}}
            {{--    --}}{{--        toastr.error(data.error, 'Error!', {--}}
            {{--    --}}{{--            positionClass: 'toast-top-center',--}}
            {{--    --}}{{--            containerId: 'toast-top-center'--}}
            {{--    --}}{{--        });--}}
            {{--    --}}{{--    }--}}
            {{--    --}}
            {{--    --}}{{--});--}}


            {{--});--}}



        });






    </script>
@endsection