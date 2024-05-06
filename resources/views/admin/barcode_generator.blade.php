@extends('admin.layout.master')

@section('title', 'Barcode Generator')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Barcode Generator
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            <div class="row mt-3 mb-2 justify-content-center">
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <select name="search_barcode_type" id="search_barcode_type" class="form-control select2">
                                            @foreach($types as $type)
                                                <option value="{{ $type->id }}">{{ $type->barcode_name }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-2">
                                    <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>
                    
                            @include('admin.inc.messages')
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;width:100% !important;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th  class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">Barcode No#</th>
                                    <th class="border-primary border-darken-1">Barcode.</th>
                                    <th class="border-primary border-darken-1">Barcode Type</th>
                                    <th class="border-primary border-darken-1">Created At</th>

                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="AddModal" data-backdrop="static" role="dialog" aria-labelledby="AddModal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Generate Barcodes</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="errormessage">

                    </div>
                    
                    <form method="post" id="add_form"
                        action="{{ route('admin.barcode_generator.submit') }}"
                        class="form-horizontal mb-1" novalidate="novalidate">
                        @csrf
                        <!-- <input type="hidden" id="sack_bag_no_check"> -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <select name="type" id="type_select" class="form-control select2"
                                      data-rule-required="true" data-msg-required="Type is required">
                                         @foreach($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->barcode_name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                                <div class="col">
                                    <div class="form-group ">
                                        <input type="text" name="prefix" class="form-control prefix" placeholder="Prefix (Alphabets Only)">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group ">
                                        <input type="number" name="from" class="form-control from" placeholder="From" data-rule-required="true" data-msg-required="From is required">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group ">
                                        <input type="number" name="to" class="form-control to" placeholder="To" data-rule-required="true" data-msg-required="To is required">
                                    </div>
                                </div>
                        </div>
                        <!-- <div class="row">
                           <div class="col-md-12">
                                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;width:100% !important;">
                                    <thead>
                                        <tr role="row" class="bg-primary white">
                                            <th class="border-primary border-darken-1">Canvas Bag No#</th>
                                            <th class="border-primary border-darken-1">Remark</th>
                                            <th class="border-primary border-darken-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sackbag_detail">
                                        <tr>
                                            <td><input id="sack_bag_no_id" type="text" name="sack_bag_no[]" class="form-control" onchange="sack_bag_check_zero(this)" ></td>
                                            <td><input type="text" name="remarks[]" class="form-control" ></td>
                                            <td><span class="btn btn-danger" id="remove_row">x</span></td>
                                        </tr>
                                    </tbody>
                                    
                                </table>
                                
                           </div>
                        </div> -->
                        <div class="form-group ml-1">
                            {{-- <button type="button" id="addrow" class="btn btn-success ">Add Row</button> --}}

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
            $('#type_select').prepend('<option value="" selected="selected">Select Type</option>').select2({
                placeholder: 'Select Type',
                width: '100%',
                dropdownParent:$('#AddModal')
            });
            
            $('#from').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#to').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_barcode_type').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Barcode Type',
                width:'100%',
                allowClear:true
            });

            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                @if (session('role_id') == 1 || count(array_intersect([981], session('permissions'))) !== 0)
                    {
                        text: '<i class="la la-plus"></i> Add New',
                        className: 'btn btn-primary request_add',
                        action: function (e, dt, node, config) {
                            $("#AddModal select").val(null).trigger('change.select2');
                            $('#AddModal input[type="text"]').val('');
                            $('#AddModal input[type="number"]').val('');
                            $('#AddModal').modal('show');
                         
                        }
                    },
                @endif
                    {
                        text: '<i class="la la-print"></i> Print',
                        className: 'btn btn-primary print',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            table.button(1).disable();
                            print(selected_rows);
                            table.rows().deselect();
                            selected_rows = [];
                        }
                    },

                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action: function (e) {
                            e.preventDefault();

                            table.rows().nodes().each(function (index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.print').enable();
                                }
                            });


                        }
                    },
                    {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action: function (e) {
                            e.preventDefault();

                            table.rows().nodes().each(function (index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.print').disable();
                                    }
                                }
                            });
                        }
                    },
                    'reset'
                ],
                scrollX: true, scrollY: '500px',
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
                ajax: {
                    url: '{{ route('admin.barcode_generator.list') }}',
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    data: function (d) {
                        d.search_barcode_type = $('#search_barcode_type').val();
                    }
                    
                },
                rowId: 'id',
                order: [[4, 'desc']],
                columns: [
                    
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        class: 'text-center align-middle select p-1',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
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
                    {data: 'barcode_key', name: 'barcode_generators.barcode_key', class: 'align-middle barcode_key'},
                    {data: 'barcode', name: 'barcode', class: 'align-middle barcode', orderable: false},
                    {data: 'barcode_name', name: 'bt.barcode_name', class: 'align-middle remarks', orderable: false},
                    {data: 'created_at', name: 'barcode_generators.created_at', class: 'align-middle created_at'},
                 
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    $('td:eq(0)', row).addClass('select-checkbox');
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function () {
                
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();
                    });

                    this.api().table().columns.adjust();
                }
            });

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

            $.validator.addMethod("prefix",
                function(value, element) {
                     // Allow empty values to pass validation
                    if (value === '') {
                        return true;
                    }
                    // Regular expression to match only alphabetic characters
                    var regex = /^[a-zA-Z]+$/;

                    // Check if the value contains only alphabetic characters
                    return regex.test(value);
                },
                "Reference Number should contain only alphabetic characters."
            );

             $('.datatable tbody').on('click', 'tr td.select-checkbox', function () {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.print').enable();
                }
                else {
                    table.button('.print').disable();
                }
            });



            function print(selected_rows) {
                $.ajax({
                        url: '{!! route('admin.barcode_generator.print_barcodes') !!}',
                        method: 'POST',
                        data: {
                            'ids[]': selected_rows,
                            '_token': '{{ csrf_token() }}'
                        }
                        })
                        .done(function (data) {

                            var tab = window.open('', '_blank');

                            if (!tab) {
                                swal({
                                    title: 'Popup Blocker Enabled!',
                                    text: 'Please add this site to your exception list.',
                                    icon: 'error',
                                    closeOnClickOutside: false,
                                    closeOnEsc: false
                                    });
                            } else {
                                    tab.document.write(data);
                                    tab.document.close();
                                    tab.focus();
                            }
                });
            }

               $("#add_form").validate({
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