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

                            <form id="track_form" class=" mb-1" novalidate="novalidate">
                                <div class="row justify-content-center">
                                  {{-- <div class="col-3">
                                        <div class="form-group">
                                            <select name="status" id="status" class="form-control select2" >
                                                @foreach($erf_status as $status)
                                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <button type="submit" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width search"><i class="la la-search"></i> Search</button>
                                        </div>
                                    </div>--}}

                                </div>
                            </form>


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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style>
        .heading_user{
            margin-top:50px;
            padding-left:35px;
        }
    </style>

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            $('#track_form #status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Search Status',
                allowClear:true
            });

            var route = '<?php echo route('admin.human_resource.erf.add'); ?>';
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: false, scrollY: '500px',
                buttons: [

                    {
                        title: 'Add ERF',
                        className: 'btn btn-primary',
                        text: '<i class="la la-plus"></i> Add ERF',
                        action:function (e) {
                            window.location = route;
                        }


                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                language: {
                    processing: data_table_loader
                },

                ajax:{
                    url: '{{ route('admin.human_resource.erf.list') }}',
                    data: function (d) {
                        d.status = $('#track_form #status').val();
                    }

                },
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'erf_id', name: 'employee_requisitions.id', class: 'align-middle erf_id'},
                    {data: 'department', name: 'dp.name', class: 'align-middle department'},
                    {data: 'designation', name: 'd.name', class: 'align-middle designation'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'city', name: 'c.name', class: 'align-middle city'},
                    {data: 'admin', name: 'a.name', class: 'align-middle admin'},
                    {data: 'status', name: 's.name', class: 'align-middle status'},
                    {data: 'action', name: 'action', class: 'align-middle action'},
                ],rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var role_select = '<select name="role_select" id="role_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.role')){
                            $(role_select).appendTo($(search))
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
                    this.api().table().columns.adjust();
                }
            });


            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var erf_id = table.row( $(this).parents('tr') ).data().erf_id;

                if ($(this).hasClass('admin_approve')) {
                    $('#erf_id').val(erf_id);
                    $('#file_modal').modal('show');
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
        });

    </script>

@endsection

