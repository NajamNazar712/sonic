@extends('admin.layout.master')

@section('title', 'Route Management')

@section('content')
    <h1 class="mb-1">
        Route Management
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Route Code</th>
                        <th class="border-primary border-darken-1">Route Title</th>
                        <th class="border-primary border-darken-1">Starting Point</th>
                        <th class="border-primary border-darken-1">End Point</th>
                        <th class="border-primary border-darken-1">Junctions</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AddRouteManagementModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRouteManagementModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Route</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="route_management_add_form" class="form-horizontal" action="{{ route('admin.settings.route_management.store') }}" method="POST" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-center">
                            <div class="col-6 form-group">
                                <label for="route_code">Route Code</label>
                                <input type="text" name="route_code" id="route_code" class="form-control route_code" placeholder="Route Code*" data-rule-required="true" data-msg-required="Route Code is required" data-rule-remote="{{ route('admin.settings.route_management.unique') }}" data-msg-remote="Route Code must be unique">
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-6 form-group">
                                <label for="route_title">Route Title</label>
                                <input type="text" name="route_title" id="route_title" class="form-control route_title" placeholder="Route Title*" data-rule-required="true" data-msg-required="Route Title is required" >
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-6 form-group">
                                <label for="starting_point_id">Starting Point</label>

                                <select class="form-control starting_point_id" name="starting_point_id" id="starting_point_id" data-rule-required="true" data-msg-required="Starting Point is required">
                                    @foreach($cities as $city)
                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row justify-content-center">

                            <div class="col-6 form-group">
                                <label for="end_point_id">End Point</label>

                                <select class="form-control end_point_id" name="end_point_id" id="end_point_id" data-rule-required="true" data-msg-required="End Point_id is required">
                                    @foreach($cities as $city)
                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                        </div>
                        <div class="row justify-content-center">

                            <div class="col-6 form-group">
                                    <label for="end_point_id">Junctions</label>
                                    <select class="form-control" name="junction[1]" id="junction_1" data-rule-required="true" data-msg-required="Junction 1 is required">
                                        @foreach($cities as $city)
                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                        @endforeach
                                    </select>
                            </div>
                            
                        </div>
                        <div id="junctions">

                        </div>

                        <div class="row justify-content-center">
                            <button type="button" class="btn btn-outline-success mr-1" title="Add more junctions" id="add_junction"><i class="la la-plus"></i>Add Junction</button>
                        </div>
                        <div class="row justify-content-center">
                            <p class="danger">Note: please add junctions in sequence!</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="AddShipperBtn" type="submit" class="btn btn-info">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <style type="text/css">
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">

function remove_junction(params) {
                console.log(params.parentElement.parentElement);
                var elem = params.parentElement.parentElement;
                elem.parentNode.removeChild(elem);
            }
    
        var index_count = 0;
        $(document).ready(function () {
            $("#starting_point_id").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Starting Point",
                width:'100%',
                dropdownParent:$('#AddRouteManagementModal')
            });
            $("#end_point_id").prepend('<option value="" selected></option>').select2({
                placeholder: "Select End Point",
                width:'100%',
                dropdownParent:$('#AddRouteManagementModal')
            });
            $("#junction_1").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select Junction 1",
                    width:'100%',
                    dropdownParent:$('#AddRouteManagementModal')
                });
                
            // var row = document.getElementById('junctions').children.length;
            // $('.remove_junction').on('click', function(param){
            //     console.log('asdsad');
            // });

             
            $('#AddRouteManagementModal #route_management_add_form #add_junction').on('click', function(){
                if(document.getElementById('junctions').children.length==0){
                 var row = 2   
                }else{
                    var row = document.getElementById('junctions').children.length+2 

                }
               var html = '<div class="row justify-content-center">\n' +
                   '                            <div class="col-6 form-group">\n' +
                   '                                <select class="form-control" name="junction[' + row + ']" id="junction_' + row + '" data-rule-required="true" data-msg-required="Junction ' + row + ' is required">\n' +
                   '                                    @foreach($cities as $city)\n' +
                   '                                        <option value="{{$city->id}}">{{$city->name}}</option>\n' +
                   '                                    @endforeach\n' +
                   '                                </select>\n' +
                   '                            </div>\n' +
                   '                        ' +
                   '         <div class="col-2" style="position: absolute;left: 580px;">\n'+
                   '  <a href="javascript:void(0);" class="btn btn-icon btn-danger" onclick="remove_junction(this)"><i class="la la-close"></i></a>\n'+
                   '                        </div>\n'+
                   '                        </div>\n';
                   
                  
                   

                $('#junctions').append(html);
                $("#junction_" + row).prepend('<option value="" selected></option>').select2({
                    placeholder: "Select Junction " + row,
                    width:'100%',
                    dropdownParent:$('#AddRouteManagementModal')
                });
                // row++;
            });
            $('#route_management_add_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Runner is being created!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.route_management.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Route Code');
                            head.push('Route Title');
                            head.push('Starting Point');
                            head.push('End Point');
                            head.push('Junctions');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.route_code);
                                row.push(values.route_title);
                                row.push(values.starting_name);
                                row.push(values.end_name);
                                row.push(values.excel_junctions);
                                row.push(values.status);

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
                    @if (session('role_id') == 1 || in_array(504, session('permissions')))
                        {
                            text: '<i class="la la-plus"></i> Add Route',
                            className: 'btn btn-primary add_route_management',
                            enabled: true,
                            action: function (e, dt, node, config) {
                                $('#AddRouteManagementModal').modal('show');
                            }
                        },
                    @endif
                    
                    {
                        extend: 'excel',
                        title: 'Route Management',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                        className: 'btn btn-primary',
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                autoWidth: false,
                ajax: '{{ route('admin.settings.route_management.list') }}',
                rowId: 'id',
                order: [[1, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle text-center serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'route_code', name: 'route_managements.route_code', class: 'align-middle text-center route_code'},
                    {data: 'route_title', name: 'route_managements.route_title', class: 'align-middle text-center route_title'},
                    {data: 'starting_id', name: 'stp.id', class: 'align-middle text-center starting_point'},
                    {data: 'end_id', name: 'endp.id', class: 'align-middle text-center end_point'},
                    {data: 'junctions', name: 'junctions', class: 'align-middle text-left junctions', orderable: false},
                    {data: 'status', name: 'route_managements.status', class: 'align-middle text-center status'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

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
                    var status_select = '<select name="status_select" id="status_select" class="form-control">' +
                        '<option value="1">Enabled</option>' +
                        '<option value="0">Disabled</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number')|| $(header).is('.action')|| $(header).is('.starting_point')|| $(header).is('.end_point')|| $(header).is('.junctions')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.status')){
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
                    });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {

                var id = table.row( $(this).parents('tr') ).data().id;

                if ($(this).hasClass('status')) {
                    $.ajax({
                        url: '{!! route('admin.settings.route_management.enable_disable') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function(data) {
                        if (data.status) {
                            table.draw(true);
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });

                        }
                    });
                }
            });


            $('body').on('click','button.edit_route_management',function () {
            $('#editRouteManagement').modal('show');
               var id = $(this).parents('tr').attr('id');
           
                $.get( "/admin/settings/route_management/"+id+"/edit/form", function( data ) {
                    $("#editRouteManagementDiv").html(data);
                });
            });
             /*$('body').on('click','button.disable',function () {*/
            /*$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = $(this).parents('tr').attr('id');
                $.ajax({
                    url: '{!! route('admin.settings.runner.enable_disable') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        'status': 0,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        table.draw();
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            });
            $('body').on('click','button.enable',function () {
                var id = $(this).parents('tr').attr('id');
                $.ajax({
                    url: '{!! route('admin.settings.runner.enable_disable') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        'status': 1,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        table.draw();
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            });*/
        });

    </script>
@endsection