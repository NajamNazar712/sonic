@extends('admin.layout.master')

@section('title', 'Sales Territory')

@section('content')
    <h1 class="mb-1">
        Sales Territory
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Name</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">Code</th>
                        <th class="border-primary border-darken-1">Users</th>
                        <th class="border-primary border-darken-1">Created by</th>
                        <th class="border-primary border-darken-1">Created at</th>
                        <th class="border-primary border-darken-1">Updated by</th>
                        <th class="border-primary border-darken-1">Updated at</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="add_territory" role="dialog" aria-labelledby="add_territory_title" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_remarks_title">Add Territory</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_territory_form" class="form-horizontal mb-1 justify-content-center" method="POST" action="{{ route('admin.sales.territory.add') }}" novalidate="novalidate">
                        {{ csrf_field()  }}
                        <div class="form-group">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Territory Name*" maxlength="50" data-rule-required="true" data-msg-required="Name is required">
                        </div>
                        <div class="form-group">
                            <select name="city" id="city" class="form-control select2" data-rule-required="true" data-msg-required="City is required">
                                @foreach($hubs as $city)
                                    <option value="{{$city->id}}"> {{$city->name}} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" name="code" id="code" class="form-control" placeholder="Territory Code*" maxlength="50" data-rule-required="true" data-msg-required="Territory Code is required">
                        </div>

                        @if(count($designations) > 0)
                            @foreach($designations as $designation)
                                <div class="form-group">
                                    <select name="designations[{{$designation->id}}][]" id="designation_{{$designation->id}}" class="form-control select2" multiple="multiple">
{{--                                        @foreach($admins as $admin)--}}
{{--                                            @if($admin->role_id == $designation->designation)--}}
{{--                                                <option value="{{$admin->id}}"> {{$admin->name}} </option>--}}
{{--                                            @endif--}}
{{--                                        @endforeach--}}
                                    </select>
                                </div>
                            @endforeach
                        @endif
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="users" role="dialog" aria-labelledby="users_title" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="users_title">User(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <style>
        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
        .show_active{
            -webkit-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -moz-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        span.font-13{
            font-size: 13px;
        }
    </style>

@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            @if(count($designations) > 0)
                @foreach($designations as $designation)
                    var designation_id = @json($designation->id);
                    var designation_code = @json($designation->code);
                    $('#add_territory_form #designation_' + designation_id).select2({
                        width: '100%',
                        placeholder: 'Select ' + designation_code,
                        allowClear:false,
                        dropdownParent:$('#add_territory_form')
                    });
                @endforeach
            @endif

                $('#add_territory_form #city').prepend('<option value="" selected="selected"></option>').select2({
                    width: '100%',
                    placeholder: 'Select City*',
                    allowClear:false,
                    dropdownParent:$('#add_territory_form')
                }).bind('select2:select', function () {
                    var city_id = parseInt($(this).val());
                    if(city_id != null && city_id != ''){
                        $.ajax({
                            url: '{!! route('admin.sales.territory.city_admins') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'city_id': city_id,
                            }
                        })
                            .done(function(data) {
                                @if(count($designations) > 0)
                                    @foreach($designations as $designation)
                                    var designation_id = @json($designation->id);
                                    var designation_code = @json($designation->code);
                                    $('#add_territory_form #designation_' + designation_id).html('').select2('destroy');

                                    $('#add_territory_form #designation_' + designation_id).select2({
                                        width: '100%',
                                        placeholder: 'Select ' + designation_code,
                                        allowClear:false,
                                        dropdownParent:$('#add_territory_form')
                                    });
                                    if(data.designations.hasOwnProperty(designation_id)){
                                        $.each(data.designations[designation_id].admins, function (index, admin) {
                                            $('#add_territory_form #designation_' + designation_id).append('<option value="' + admin['id'] + '">' + admin['name'] + '</option>');
                                        });
                                    }
                                    @endforeach
                                @endif
                            });
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
                        url: '{{ route('admin.sales.territory.list') }}',
                        data: params,
                        success: function (result) {

                            head = [];

                            head.push('S. No.');
                            head.push('Name');
                            head.push('City');
                            head.push('Code');
                            head.push('Created by');
                            head.push('Created at');
                            head.push('Updated by');
                            head.push('Updated at');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.city);
                                row.push(values.code);
                                row.push(values.created_by);
                                row.push(values.created_at);
                                row.push(values.updated_by);
                                row.push(values.updated_at);
                                row.push(values.status);
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
                scrollX: true, scrollY: '500px',
                // autoWidth: false,
                buttons: [
                        @if (session('role_id') == 1 || in_array(626, session('permissions')))
                    {
                        text: 'Add Territory',
                        className: 'btn btn-primary add',
                        action: function (e, dt, node, config) {
                            $('#add_territory').modal('show');
                        }
                    },
                        @endif
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Sales Incentive Territory',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                autoWidth: false,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.sales.territory.list') }}',
                },
                order: [[8, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'name' ,name: 'sales_territories.name', class: 'align-middle text-center name'},
                    { data:'city' ,name: 'c.name', class: 'align-middle text-center city'},
                    { data:'code' ,name: 'sales_territories.code', class: 'align-middle text-center code'},
                    { data:'users' ,name: 'users', class: 'align-middle text-center users', orderable: false, searchable: false},
                    { data:'created_by' ,name: 'a.name', class: 'align-middle text-center created_by'},
                    { data:'created_at' ,name: 'sales_territories.created_at', class: 'align-middle text-center created_at'},
                    { data:'updated_by' ,name: 'b.name', class: 'align-middle text-center updated_by'},
                    { data:'updated_at' ,name: 'sales_territories.updated_at', class: 'align-middle text-center updated_at'},
                    { data:'status' ,name: 'sales_territories.status', class: 'align-middle text-center status'},
                    { data:'action' ,name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false},

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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="1">Active</option>' +
                        '<option value="0">In-Active</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
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
                        $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                        });
                    });

                    this.api().table().columns.adjust();
                }
            });
            $('#add_territory').on('hide.bs.modal', function () {
                $('#city').val(null).trigger('change');
                $('#name').val('');
                $('#code').val('');
            });

            $('#add_territory_form').validate({
                ignore: ":not(:visible),:disabled",
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Territory is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.enable', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url: '{!! route('admin.sales.territory.status') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id,
                        'status': 1
                    }
                }).done(function(data){
                    if(data.status){
                        table.draw(true);
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.disable', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url: '{!! route('admin.sales.territory.status') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id,
                        'status': 0
                    }
                }).done(function(data){
                    if(data.status){
                        table.draw(true);
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });
            });
            $("#editterritory").on("show.bs.modal", function(e) {
                var $invoker = $(e.relatedTarget);
                var action = $invoker.attr('rel');
                var id = $(e.relatedTarget).data('target-id');

                if(action == 'editterritory'){
                    $.get( "/admin/sales/territory/edit/"+id+"", function( data ) {
                        $("#editterritoryDiv").html(data);
                    });
                }
            });

            $('#datatable tbody').on('click','tr td.users button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#users .modal-body').html('');
                $.ajax({
                    url: '{!! route('admin.sales.territory.users') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        if (data.status == 1) {
                            var admins = '';
                            if (data.details) {
                                $.each(data.details, function(index, detail) {
                                    admins += '<b>' + detail.designation + ' (' + detail.code + ')</b><br>';
                                    $.each(detail.admins, function(index, admin) {
                                        admins += admin + '<br>';
                                    });
                                    admins += '<br>'
                                });
                            }
                            $('#users .modal-body').html(admins);
                            $('#users').modal('show');
                        }
                        else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                        }
                    });

            });
        });

    </script>
@endsection