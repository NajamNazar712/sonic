@extends('admin.layout.master')

@section('title', 'Rider Category Request')

@section('content')
    <h1>Rider Category ByPass Request</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">Rider ID</th>
                                    <th class="border-primary border-darken-1">Rider Type</th>
                                    <th class="border-primary border-darken-1">Rider</th>
                                    <th class="border-primary border-darken-1">Reason</th>
                                    <th class="border-primary border-darken-1">Requested Date</th>
                                    <th class="border-primary border-darken-1">Requested By</th>
                                    <th class="border-primary border-darken-1">Approved Date</th>
                                    <th class="border-primary border-darken-1">Approved By</th>
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
    </section>
    <div class="modal fade" id="request_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="request_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Add Rider Category Bypass Request</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <form method="post" id="request_form" novalidate="novalidate" action="{{route('admin.delivery.note.rider_category_submit')}}">
                        @method('POST')
                        @csrf
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                <label><strong>Rider</strong></label>
                                <fieldset class="form-group">
                                    <select name="rider_cat" id="rider_cat" class="form-control select2" data-rule-required="true" data-msg-required="Rider category is required">
                                        @foreach($riders_cat as $rider_cat)
                                            <option value="{{$rider_cat->id}}">{{$rider_cat->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                <label><strong>Rider</strong></label>
                                <fieldset class="form-group">
                                    <select name="rider_id" id="rider_id" class="form-control select2" data-rule-required="true" data-msg-required="Rider is required">
                                    </select>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row justify-content-center">

                            <div class="form-group">

                                <textarea type="text" rows="5" class="form-control" cols="90" id="reason" name="reason" placeholder="Enter Reason" data-rule-required="true" data-msg-required="Reason is required"></textarea>
                            </div>

                        </div>
                        <div class="form-group text-center mt-2">
                            <button type="submit" class="btn btn-primary" id="form_btn">Submit</button>
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
            width: 100%;
        }

        .selectize-control .selectize-input {
            vertical-align: middle;
        }

        .selectize-control .selectize-input .item {
            word-break: break-all;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>

        $(document).ready(function() {
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });
            $('#rider_cat').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Rider Category',
                width:'100%',
                allowClear:true
            }).bind('change', function () {
                var rider_cat = this.value;
                if(rider_cat == null || rider_cat == ''){
                    return false;
                }
                $.ajax({
                    url: '{!! route('admin.delivery.note.check_rider_cat') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'rider_cat':rider_cat
                    }
                }).done(function (data) {

                    if (data.status == 1) {

                        $('#rider_id').empty();
                        $.each(data.rider, function(index, values) {

                            var html = `
                                <option value='${values.id}'>${values.name}</option>
                            `
                            $('#rider_id').append(html);

                        });

                    } else {
                        toastr.error(data.error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                        $('#form_btn').attr('disabled' , true);
                    }

                });
            });
            $('#rider_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Rider',
                width:'100%',
                dropdownParent: $("#request_form")
            });
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.delivery.note.rider_cat_request_list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Rider ID');
                            head.push('Rider');
                            head.push('Hub');
                            head.push('Reason');
                            head.push('Requested Date');
                            head.push('Requested By');
                            head.push('Approved Date');
                            head.push('Approved By');
                            head.push('Status');

                            $.each(result.data, function (index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.rider_id);
                                row.push(values.rider);
                                row.push(values.hub);
                                row.push(values.reason);
                                row.push(values.requested_at);
                                row.push(values.requested_by);
                                row.push(values.approved_at);
                                row.push(values.approved_by);
                                row.push(values.status);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });


            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                        @if (session('role_id') == 1 || in_array(538, session('permissions')))
                    {
                        title: 'Add Request',
                        className: 'btn btn-primary',
                        text: '<i class="la la-plus"></i> Add Request',
                        action: function (e) {
                            $('#request_modal').modal('show');

                        }
                    },
                        @endif
                    {
                        extend: 'excel',
                        title: 'DN ByPass Request',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                scrollX: false, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.delivery.note.rider_cat_request_list') }}',
                    method:'get',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.search_hub = $('#search_hub').val();
                    }
                },
                rowId: 'shId',
                order: [[8, 'desc']],
                columns: [
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'rider_id', name: 'r.id', class: 'align-middle rider_id'},
                    {data: 'rider', name: 'r.name', class: 'align-middle rider'},
                    {data: 'rider_type', name: 'rider_type', class: 'align-middle rider_type'},
                    {data: 'reason', name: 'reason', class: 'align-middle reason'},
                    {data: 'requested_at', name: 'requested_at', class: 'align-middle requested_at'},
                    {data: 'requested_by', name: 'a.name', class: 'align-middle requested_by'},
                    {data: 'approved_at', name: '.approved_at', class: 'align-middle approved_at'},
                    {data: 'approved_by', name: 'a.name', class: 'align-middle approved_by'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'action', name: 'action', class: 'align-middle action', orderable: false, sortable: false},


                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control">' +
                        '</select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.action') || $(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        } else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });

                    this.api().table().columns.adjust();
                }
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function () {

                var id = table.row($(this).parents('tr')).data().id;

                if ($(this).hasClass('approve_request')) {
                    $.ajax({
                        url: '{!! route('admin.delivery.note.rider_category_approve') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': id,
                        }
                    }).done(function (data) {

                        if (data.status == 1) {
                            table.draw();
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        } else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }

                    });
                }
            });
            // $('#request_modal').on('hide.bs.modal', function (e) {
            //     $('#request_modal #rider_id').val('').trigger('change');
            //     $('#request_modal #amount').val('');
            //     $('#request_modal #reason').val('');
            //     $('#request_modal #dncc').val('');
            // });

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });
        });


        $("#request_form").validate({

            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {

                swal({
                    title: 'Please Wait!',
                    text: 'Request is being submitted!',
                    icon: 'info',
                    buttons: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false
                });

                form.submit();
            }
        });
    </script>
@endsection