@extends('admin.layout.master')
@section('title','Product Type')

@section('content')
    <h1 class="mb-1">
        Product Type
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <form id="add_product_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                    <div class="form-group">
                        <input type="text" name="product_name" class="form-control product_name" placeholder="Add Product" data-rule-required="true" data-msg-required="Product Name is required">
                    </div>
                    <div class="form-group ml-1">
                        <button type="submit" id="add_product" name="add" class="btn btn-primary add" value="Add">Add</button>
                    </div>
                </form>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3; width:100%;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        {{-- <th class="border-primary border-darken-1"></th> --}}
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Name</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <style>
        table.dataTable {
            font-size: 12px;
        }

        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
            word-wrap: break-word;
        }

        table.dataTable thead tr th:before,
        table.dataTable thead tr th:after {
            height: 20px;
            margin-bottom: -10px;
            bottom: 50% !important;
        }

        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }
        .selectize-control {
            width: 300px !important;
        }
        .goldClass{
            background-color: gold;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        var selected_rows = [];
        var restricted_rows = [];
        $(document).ready(function () {

            $('#add_product_form input.product_name').focus();

            // $('#add_product_form input.product_name').inputmask({
            //     mask: 'aaaaa*', // Allows up to 50 alphabetic characters
            //     definitions: {
            //         '*': {
            //             validator: '[A-Za-z]',
            //             cardinality: 1,
            //             casing: 'upper' // Optional: converts characters to uppercase
            //         }
            //     }
            // });

            $('#add_product_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    $('#add_product_form button.add').prop('disabled', true);

                    var product_name = $(form).find('input.product_name').val();
                    if (table.columns('.product_name').data().eq(0).indexOf(parseInt(product_name)) === -1) {
                        $.ajax({
                            url: '{!! route('admin.settings.product_type.add') !!}',
                            method: 'POST',
                            data: {
                                'product_name': product_name,
                                '_token': '{{ csrf_token() }}'
                            },
                            timeout: 5000,
                            error: function (data) {
                                form.reset();

                                $('#add_product_form input.product_name').val('').focus();

                                $('#add_product_form button.add').prop('disabled', false);
                                scan_sound(2);
                                toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            },
                            success: function(data) {
                                form.reset();

                                $('#add_product_form input.product_name').val('').focus();

                                remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';

                                if (data.status == 0) {
                                    
                                    toastr.success(data.success, data.message, {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    table.draw();
                                }
                                else {
                                    $('#add_product_form button.add').prop('disabled', false);
                                    scan_sound(2);
                                    toastr.error(data.error, data.message, {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }

                                $('#add_product_form button.add').prop('disabled', false);
                            }
                        });
                    }
                    else {
                        $('#add_product_form button.add').prop('disabled', false);

                        toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
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
                        url: '{{ route('admin.settings.product_type.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Name');
                            
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.product_name);
                                
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Product Types',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                
                lengthMenu: [[5,50, 100, 500, 1000, -1], [5,50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url:'{{ route('admin.settings.product_type.list') }}',
                },
               
                order: [[1, 'desc']],
                columns: [
                    // {data: 'shId', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id', defaultContent:'', orderable: false, searchable: false, class: 'align-middle serial_number'},
                    {data: 'product_name', name: 'product_name', class: 'align-middle product_name'},
                    // {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    // $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.shId, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                   
                },
            });
                
            //Selectize
            // var select = $('#tracking_number').selectize({
            //     placeholder: 'Add Product',
            //     delimiter: ',',
            //     createOnBlur: true,
            //     persist: false,
            //     plugins: ['remove_button'],
            //     onDropdownOpen: function(dropdown) {
            //         dropdown.remove();
            //     },
            //     onType: function(str) {
            //         var regex = /^[0-9,]+$/;

            //         if (!regex.test(str)) {
            //             select[0].selectize.setTextboxValue('');
            //         }
            //     },
            //     create: function(input) {
            //         if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
            //             return {
            //                 value: input,
            //                 text: input
            //             }
            //         }
            //         else {
            //             return false;
            //         }
            //     },
            // });

           
        });
    </script>
@endsection