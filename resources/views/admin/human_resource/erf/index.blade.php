@extends('admin.layout.master')

@section('title', 'ERF Dashboard')

@section('content')
    <h1>ERF Dashboard</h1>

    <section>
        <div class="row">

            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <select name="search_status" id="search_status" class="form-control select2">
                                        @foreach($erf_status as $status)
                                            <option value="{{$status->id}}">{{$status->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">ERF ID</th>
                                    <th class="border-primary border-darken-1">Department</th>
                                    <th class="border-primary border-darken-1">Designation</th>
                                    <th class="border-primary border-darken-1">Hub</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Line Manager</th>
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

    <div class="modal fade" id="file_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="file_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Input File</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <form method="post" id="file_upload" enctype="multipart/form-data" novalidate="novalidate" action="{{route('admin.human_resource.erf.file_upload')}}">
                        @method('POST')
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="erf_id" id="erf_id">
                            <input type="file" class="form-control" id="file" name="file" placeholder="Select File" data-rule-required="true" data-msg-required="File is required">
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

    <script type="text/javascript">
        $('#search_status').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder: 'Search Status',
            allowClear:true
        }).bind('change', function() {
            table.draw();
        });
        jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            if ( this.context.length ) {
                body = [];
                var params = table.ajax.params();
                params.start = 0;
                params.length = -1;
                params.excel = true;
                var jsonResult = $.ajax({
                    url: '{{ route('admin.human_resource.erf.list') }}',
                    data: params,
                    success: function (result) {
                        head = [];

                        head.push('S.No');
                        head.push('ERF ID');
                        head.push('Department');
                        head.push('Designation');
                        head.push('Hub');
                        head.push('City');
                        head.push('Line Manager');
                        head.push('Status');

                        $.each(result.data, function(index, values) {
                            row = [];

                            row.push(index + 1);
                            row.push(values.erf_id);
                            row.push(values.department);
                            row.push(values.designation);
                            row.push(values.hub);
                            row.push(values.city);
                            row.push(values.admin);
                            row.push(values.status);
                            body.push(row);
                        });
                    },
                    async: false
                });

                return {body: body, header: head};
            }
        } );

        var route = '<?php echo route('admin.human_resource.erf.add'); ?>';
        var table = $('#datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            buttons: [
                {
                    title: 'Add ERF',
                    className: 'btn btn-primary',
                    text: '<i class="la la-plus"></i> Add ERF',
                    action: function (e) {
                        window.location = route;

                    }
                },
                
                {
                    extend: 'excel',
                    title: 'ERF List',
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
            ajax:{
                url: '{{ route('admin.human_resource.erf.list') }}',
                data: function (d) {
                    d.search_status = $('#search_status').val();
                }
            },
            rowId: 'shId',
            order: [[1, 'desc']],
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

                {data: 'erf_id', name: 'employee_requisitions.id', class: 'align-middle erf_id'},
                {data: 'department', name: 'dp.name', class: 'align-middle department'},
                {data: 'designation', name: 'd.name', class: 'align-middle designation'},
                {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                {data: 'city', name: 'c.name', class: 'align-middle city'},
                {data: 'admin', name: 'a.name', class: 'align-middle admin'},
                {data: 'status', name: 's.name', class: 'align-middle status'},
                {data: 'action', name: 'action', class: 'align-middle action'},


            ],
            rowCallback: function(row, data, index) {
                var info = table.page.info();
                $('td:eq(0)', row).html(index + 1 + info.page * info.length);

            },
            initComplete: function() {
                var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control">' +
                    '</select>';
                var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();


                    if ($(header).is('.action') || $(header).is('.serial_number')) {
                        $(td).appendTo($(search));
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
                
                this.api().table().columns.adjust();
            }
        });
        $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function () {
            var erf_id = table.row($(this).parents('tr')).data().erf_id;

            if ($(this).hasClass('admin_approve')) {
                $('#erf_id').val(erf_id);
                $('#file_modal').modal('show');
            }
        });


        $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function () {

            var erf_id = table.row($(this).parents('tr')).data().erf_id;

            if ($(this).hasClass('approve_request')) {
                $.ajax({
                    url: '{!! route('admin.human_resource.erf.approve') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': erf_id,

                    }
                }).done(function (data) {

                    if (data.status == 1) {
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
        


        $("#file_upload").validate({

            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {

                swal({
                    title: 'Please Wait!',
                    text: 'Route is being updated!',
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