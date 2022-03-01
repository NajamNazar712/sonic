@extends('admin.layout.master')

@section('title', 'Territory')

@section('content')
    <h1>Territory List</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        @include('admin.inc.messages')
                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
{{--                                    <th class="border-primary border-darken-1"></th>--}}
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">Name</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Created By</th>
                                    <th class="border-primary border-darken-1">Created At</th>
                                    <th class="border-primary border-darken-1">Updated By</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
                                    <th class="border-primary border-darken-1">Action</th>

                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade text-left" id="AddTerritory" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddTerritory"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Add Territory</h4>
                    <button type="button" class="close add_modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="territory_form" class="form-horizontal" method="POST" action="{{ route('admin.management.territory.store') }}" novalidate="novalidate">
                        {{ csrf_field() }}

                        <div class="row justify-content-center">
                            <div class="col text-center">
                                <div class="form-group">
                                    <select name="city_id" class="select2" id="city_id" data-rule-required="true" data-msg-required="City is required">
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col text-center">
                                <div class="form-group">
                                    <input type="text" name="territory" id="territory_name" class="form-control text-center" placeholder="Territory Name*" data-rule-required="true" data-msg-required="Territory Name is required">
                                </div>
                            </div>
                        </div>


                        <div class="row justify-content-center">
                            <div class="col">
                                <div class="form-group text-center mt-2">
                                    <button type="submit" class="btn btn-primary">Add</button>
                                </div>
                            </div>
                        </div>
                        
                    </form>
            </div>
        </div>
    </div>
    </div>

        <div class="modal fade text-left" id="EditTerritory" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditTerritory"
             aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="">Edit Territory</h4>
                        <button type="button" class="close edit_modal" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="edit_territory_form" class="form-horizontal" method="POST" action="#" novalidate="novalidate">
                            {{csrf_field()}}
                            @method('PUT')
                            <div class="row justify-content-center">
                                <div class="col text-center">
                                    <div class="form-group">
                                        <select name="city_id" class="select2" id="edit_city_id" data-rule-required="true" data-msg-required="City is required">
                                            @foreach($cities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col text-center">
                                    <div class="form-group">
                                        <input type="text" name="territory" id="edit_territory" class="form-control text-center" placeholder="Territory Name*" data-rule-required="true" data-msg-required="Territory Name is required">
                                    </div>
                                </div>
                            </div>


                            <div class="row justify-content-center">
                                <div class="col">
                                    <div class="form-group text-center mt-2">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection


@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            $('.add_modal').on('click',function(){
                $('#city_id').val('').trigger('change');
                $('#territory_name').val('').trigger('change');
            });
            $('#city_id').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*',
                dropdownParent: $("#AddTerritory")
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    if(params !== undefined){
                        params.start = 0;
                        params.length = -1;
                        params.excel = true;
                    }

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.management.territory.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Name');
                            head.push('City');
                            head.push('status');
                            head.push('Created By');
                            head.push('Created At');
                            head.push('Updated By');
                            head.push('Updated At');

                            $.each(result.data, function(index, values) {
                            row = [];

                            row.push(index + 1);
                                row.push(values.name);
                                row.push(values.city);
                                row.push(values.status);
                                row.push(values.created_by);
                                row.push(values.created_at);
                                row.push(values.updated_by);
                                row.push(values.updated_at);
                            body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );

            var selected_rows = [];
            var table =  $('.datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',

                buttons: [{
                    text: 'Add Territory',
                    className: 'btn btn-primary',
                    enabled: true,
                    action: function (e, dt, node, config) {

                        $('#AddTerritory').modal('show');
                    }

                 },
                    {
                        extend: 'excel',
                        title: 'Territory List',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'],
                scrollX: false, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.management.territory.list') }}',
                rowId: 'id',
                order: [[5, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'territories.name', class: 'align-middle name'},
                    {data: 'city', name: 'c.name', class: 'align-middle city'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'created_by', name: 'a.name', class: 'align-middle created_by'},
                    {data: 'created_at', name: 'territories.created_at', class: 'align-middle created_at'},
                    {data: 'updated_by', name: 'ad.name', class: 'align-middle updated_by'},
                    {data: 'updated_at', name: 'territories.updated_at', class: 'align-middle updated_at'},
                    {orderable: false,data: 'action', name: 'action', class: 'align-middle action',},
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
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.select') ) {
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


            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));
                console.log(id);
                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.assign_rider').enable();
                    table.button('.tag').enable();
                }
                else {
                    table.button('.assign_rider').disable();
                    table.button('.tag').disable();
                }
            });

            $('#edit_territory_form #edit_city_id').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*',
                dropdownParent: $("#EditTerritory")
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var territory_id = table.row( $(this).parents('tr') ).data().id;
                console.log(territory_id);
                if ($(this).hasClass('edit')) {
                    $.ajax({
                        url: '{!! route('admin.management.territory.edit') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': territory_id
                        }
                    }).done(function(data){

                        if(data.details.length != 0 ){

                            var city_id = data.details.city_id;
                            var name = data.details.name;

                            $('#edit_territory_form #edit_city_id').val(city_id).trigger('change');
                            $('#edit_territory_form #edit_territory').val(name);

                            $('#EditTerritory').modal('show');
                            var route = '{!! route('admin.management.territory.update', ':id') !!}';
                            console.log(route);
                            route = route.replace(':id', territory_id);
                            console.log(route);
                            $("#edit_territory_form").attr('action', route);

                        }

                    });

                }
                if ($(this).hasClass('disable_territory')) {
                    $.ajax({
                        url: '{!! route('admin.management.territory.disable_territory') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': territory_id,
                            'status':status
                        }
                    })
                        .done(function(data){

                            if(data.status == 1 )
                            {
                                table.draw(false);
                                toastr.success(data.success, 'Success!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                            }
                            else{
                                toastr.error(data.error, 'Error!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                            }

                        });

                }
                if ($(this).hasClass('enable_territory')) {
                    $.ajax({
                        url: '{!! route('admin.management.territory.enable_territory') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': territory_id,
                            'status':status
                        }
                    })
                        .done(function(data){

                            if(data.status == 1 )
                            {
                                table.draw(false);
                                toastr.success(data.success, 'Success!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                            }
                            else{
                                toastr.error(data.error, 'Error!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                            }

                        });

                }
            });

            $('#territory_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function (value) {
                    return $.trim(value);
                },
                errorPlacement: function (error, element) {
                    error.addClass('w-100','text-center').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'User is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });


            $('#edit_territory_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function (value) {
                    return $.trim(value);
                },
                errorPlacement: function (error, element) {
                    error.addClass('w-100','text-center').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'User is being added!',
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