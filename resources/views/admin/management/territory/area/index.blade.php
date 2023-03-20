@extends('admin.layout.master')

@section('title', 'Area')

@section('content')
    <h1>Area List</h1>

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
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">Area</th>
                                    <th class="border-primary border-darken-1">Territory</th>
                                    <th class="border-primary border-darken-1">Created At</th>
                                    <th class="border-primary border-darken-1">Created By</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
                                    <th class="border-primary border-darken-1">Updated By</th>
                                    <th class="border-primary border-darken-1">Status</th>
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
    <div class="modal fade text-left" id="TerritoryTag" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="TerritoryTagModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag Territory</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <select name="territory" id="tag_territory" class="form-control select2">
                            @foreach($territories as $territory)
                                <option value="{{ $territory->id }}" > {{ $territory->name }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="territoryTagSubmit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AddArea" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddArea" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Add Area</h4>
                    <button type="button" class="close close_add_modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="territory_form" class="form-horizontal" method="POST" action="{{ route('admin.management.area.store') }}" novalidate="novalidate">
                        {{ csrf_field() }}

                        <div class="row justify-content-center">
                            <div class="col text-center">
                                <div class="form-group">
                                    <select name="territory" class="select2" id="add_territory" data-rule-required="true" data-msg-required="Territory is required">
                                        @foreach($territories as $territory)
                                            <option value="{{ $territory->id }}">{{ $territory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col text-center">
                                <div class="form-group">
                                    <input type="text" name="area" id="area" class="form-control text-center" placeholder="Area Name*" data-rule-required="true" data-msg-required="Area Name is required">
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

    <div class="modal fade text-left" id="EditArea" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditArea" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Edit Area</h4>
                    <button type="button" class="close close_add_modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit_area_form" class="form-horizontal " method="POST" action="#" novalidate="novalidate">
                        {{ csrf_field() }}
                        @method('PUT')
                        <div class="row justify-content-center">
                            <div class="col text-center">
                                <div class="form-group">
                                    <select name="territory" class="select2" id="edit_territory" data-rule-required="true" data-msg-required="Territory is required">
                                        @foreach($territories as $territory)
                                            <option value="{{ $territory->id }}">{{ $territory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col text-center">
                                <div class="form-group">
                                    <input type="text" name="area" id="edit_area" class="form-control text-center" placeholder="Area Name*" data-rule-required="true" data-msg-required="Area Name is required">
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
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

            $('#AddArea .close_add_modal').on('click', function (e) {
                $('#territory_form #add_territory').val('').trigger('change');
                $('#territory_form #area').val('').trigger('change');

            });

            $('#territory_form #add_territory').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Territory*' ,
                dropdownParent: $('#AddArea')
            });

            $("#edit_area_form #edit_territory").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Territory",
                width:'100%',
                dropdownParent:$('#EditArea')
            });

            $("#TerritoryTag #tag_territory").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Territory",
                width:'100%',
                dropdownParent:$('#TerritoryTag')
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
                        url: '{{ route('admin.management.area.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Area');
                            head.push('Territory');
                            head.push('Created At');
                            head.push('Created By');
                            head.push('Updated At');
                            head.push('Updated At');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.area);
                                row.push(values.territory);
                                row.push(values.created_at);
                                row.push(values.created_by);
                                row.push(values.updated_at);
                                row.push(values.updated_by);
                                row.push(values.area_status);
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
                    text: 'Territory Tagging',
                    className: 'btn btn-primary territory_tagging',
                    enabled:false,
                    action: function (e, dt, node, config) {
                        if (selected_rows != '') {

                            $('#TerritoryTag').modal('show');
                            $('#territoryTagSubmit').on('click', function () {
                                var territory = parseInt($('#tag_territory').val());

                                swal({
                                    text: 'Are you sure, you want to Tag?',
                                    icon: 'info',
                                    buttons: {
                                        cancel: {
                                            text: 'No',
                                            value: null,
                                            visible: true,
                                            closeModal: true,
                                        },
                                        confirm: {
                                            text: 'Yes',
                                            value: true,
                                            visible: true,
                                            closeModal: true
                                        }
                                    },
                                    closeOnClickOutside: false,
                                    closeOnEsc: false,
                                    dangerMode: true
                                }).then(function (confirm) {
                                    if (confirm) {
                                        if (territory) {
                                            $.ajax({
                                                url: '{!! route('admin.management.area.tag') !!}',
                                                method: 'POST',
                                                data: {
                                                    'territory': territory,
                                                    'areas[]': selected_rows,
                                                    '_token': '{{ csrf_token() }}'
                                                }
                                            })
                                                .done(function (data) {
                                                    console.log(data.status);
                                                    if (data.status == 1) {
                                                        $('#TerritoryTag').modal('hide');
                                                        toastr.success(data.success, 'Success!', {
                                                            positionClass: 'toast-bottom-center',
                                                            containerId: 'toast-bottom-center'
                                                        });
                                                    } else {
                                                        toastr.error(data.error, 'Error!', {
                                                            positionClass: 'toast-top-center',
                                                            containerId: 'toast-top-center'
                                                        });
                                                    }
                                                    selected_rows = [];
                                                    table.rows().deselect();
                                                    $('#territory').val('').trigger('change');
                                                    $('#TerritoryTag').modal('hide');
                                                    table.draw(true);
                                                    table.button('.territory_tagging').disable();

                                                });
                                        } else {
                                            toastr.error(error, 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center'
                                            });
                                        }
                                    }
                                });
                            });

                        } else {
                            var error = "Account Not selected!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    }
                }
                    ,{
                        text: 'Add Area',
                        className: 'btn btn-primary',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#AddArea').modal('show');
                        }

                    },
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());

                                    territory_id  = $(row.node()).data('id');

                                    var allow = false;

                                    if(territory_ids.length == 0) {
                                        territory_ids.push(territory_id);

                                        allow = true;
                                    }
                                    else if(territory_ids[0] == territory_id) {
                                        allow = true;
                                    }

                                    if (allow) {
                                        row.select();

                                        var index = $.inArray(id, selected_rows);

                                        if (index === -1) {
                                            selected_rows.push(id);
                                        }

                                        table.button('.territory_tagging').enable();

                                    }
                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.territory_tagging').disable();

                                        territory_ids.splice(index, 1);
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Area List',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'],
                scrollX: false, scrollY: '500px',
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
                ajax: '{{ route('admin.management.area.list') }}',
                rowId: 'id',
                order: [[4, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'area', name: 'area_territories.name', class: 'align-middle area'},
                    {data: 'territory', name: 't.name', class: 'align-middle territory'},
                    {data: 'created_at', name: 'area_territories.created_at', class: 'align-middle created_at'},
                    {data: 'created_by', name: 'a.created_by', class: 'align-middle created_by'},
                    {data: 'updated_at', name: 'area_territories.updated_at', class: 'align-middle updated_at'},
                    {data: 'updated_by', name: 'ad.updated_at', class: 'align-middle created_by'},
                    {data: 'area_status', name: 'area_status', class: 'align-middle area_status'},
                    {data: 'action', orderable: false, name: 'action', class: 'align-middle action',},
                ],
                rowCallback: function(row, data, index) {

                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

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
            var territory_ids = [];





            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));
                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.territory_tagging').enable();
                }
                else {
                    table.button('.territory_tagging').disable();

                }
            });

            
            $('#TerritoryTag').on('hide.bs.modal', function (e) {
                $('#territory').val('').trigger('change');
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var area_id = table.row( $(this).parents('tr') ).data().id;
                console.log(area_id);
                if ($(this).hasClass('edit')) {
                    $.ajax({
                        url: '{!! route('admin.management.area.edit') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': area_id
                        }
                    }).done(function(data){

                        if(data.details.length != 0 ){

                            var territory_id = data.details.territory_id;
                            var name = data.details.name;

                            $('#edit_area_form #edit_territory').val(territory_id).trigger('change');
                            $('#edit_area_form #edit_area').val(name);

                            $('#EditArea').modal('show');
                            var route = '{!! route('admin.management.area.update', ':id') !!}';
                            console.log(route);
                            route = route.replace(':id', area_id);
                            console.log(route);
                            $("#edit_area_form").attr('action', route);

                        }

                    });

                }
                if ($(this).hasClass('disable_area_status')) {
                    $.ajax({
                        url: '{!! route('admin.management.area.disable_area_status') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': area_id
                        }
                    }).done(function(data){

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
                if ($(this).hasClass('enable_area_status')) {
                    $.ajax({
                        url: '{!! route('admin.management.area.enable_area_status') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': area_id
                        }
                    }).done(function(data){

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


                $('#edit_area_form').validate({
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