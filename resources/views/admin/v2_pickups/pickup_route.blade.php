@extends('admin.layout.master')

@section('title', 'Pickup Routes')

@section('content')

    <h1>Pickup Routes </h1>

    <section>

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">City Name</th>
                                    <th class="border-primary border-darken-1">Route Code</th>
                                    <th class="border-primary border-darken-1">Rider Trax ID</th>
                                    <th class="border-primary border-darken-1">Rider Name</th>
                                    <th class="border-primary border-darken-1">Start Point</th>
                                    <th class="border-primary border-darken-1">End Point</th>
                                    <th class="border-primary border-darken-1">Junction</th>
                                    <th class="border-primary border-darken-1">Added Date/Time</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    {{--   <th class="border-primary border-darken-1">Route Types</th>--}}
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div style="display: none;">
                        <form id="active_route_form" action="{{route('admin.management.route.status')}}" method="post" class="mt-2">
                            {{csrf_field()}}
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="cid" id="cid">
                            <input type="hidden" name="status" id="cstatus">
                            <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">Yes</button>
                            <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade text-left" id="add_route" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRoute"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h4 class="modal-title white">Add Route</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body ">

                 <form action="{{route('admin.management.route.add')}}" method="post" class="mt-2" id="addRouteForm" novalidate="novalidate">
                    {{csrf_field()}}

                    <div class="row mb-2">
                        <div class="col">
                            <fieldset class="form-group">
                                <select name="city_id" id="city_list" class="form-control select2" style="width: 100%;text-align: left; " required data-rule-required="true" data-msg-required="This field is required">
                                    <option value="" selected>Select a City</option>
                                    @foreach($cities as $city)
                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col">
                            <fieldset class="form-group">
                                <input type="text" class="form-control" name="route_code" placeholder="Route Code" required data-rule-required="true" data-msg-required="This field is required">
                            </fieldset>

                        </div>
                        <div class="col">
                            <fieldset class="form-group">
                                <input type="text" class="form-control" name="start"  id="startSearchTextField" placeholder="Start Point" required data-rule-required="true" data-msg-required="This field is required">
                            </fieldset>
                        </div>
                        <div class="col">
                            <fieldset class="form-group">
                                <input type="text" class="form-control" name="end"  placeholder="End Point" required data-rule-required="true" data-msg-required="This field is required">
                            </fieldset>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <fieldset class="form-group">
                                <select name="rider_id" id="rider" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                                    <option value="" selected>Select a Rider</option>
                                    @foreach($riders as $rider)
                                        <option value="{{$rider->id}}">{{$rider->name}} - {{$rider->trax_id}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                    </div>

                     <input type="text"  name="route_type_id" hidden value="1">

                    <div class="row mb-2">
                        <div class="col">
                            <fieldset class="form-group">
                                <textarea name="junction" class="form-control" placeholder="Add Junctions (comma seperated)" id="junction" cols="30" rows="5" required data-rule-required="true" data-msg-required="This field is required"></textarea>
                            </fieldset>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning btn-min-width mr-1 mb-1" id="confirmAction">Add Route</button>
                        <button type="button" class="btn btn-primary btn-min-width mr-1 mb-1" data-dismiss="modal">Cancel</button>

                    </div>
                </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade text-left" id="edit_route_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditRoute"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h4 class="modal-title white">Edit Route</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form action="#" method="post" class="mt-2" id="editRouteForm" novalidate="novalidate">
                            {{csrf_field()}}
                            @method('PUT')
                            <div class="row mb-2">
                                <div class="col">
                                    <fieldset class="form-group">
                                        <select name="city_id" id="city_id" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                                            <option value="" selected>Select a City</option>
                                            @foreach($cities as $city)
                                                <option value="{{$city->id}}">{{$city->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" id="route_code" name="route_code" placeholder="Route Code" required data-rule-required="true" data-msg-required="This field is required">
                                    </fieldset>

                                </div>
                                <div class="col">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="start"  id="start" placeholder="Start Point" required data-rule-required="true" data-msg-required="This field is required">
                                    </fieldset>
                                </div>
                                <div class="col">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="end"  id="end" placeholder="End Point" required data-rule-required="true" data-msg-required="This field is required">
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <fieldset class="form-group">
                                        <select name="rider_id" id="rider_id" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                                            <option value="" selected>Select a Rider</option>
                                            @foreach($riders as $rider)
                                                <option value="{{$rider->id}}">{{$rider->name}} - {{$rider->trax_id}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>

                            <input type="text" name="route_type_id" hidden value="1">

                            <div class="row mb-2">
                                <div class="col">
                                    <fieldset class="form-group">
                                        <textarea name="junction" class="form-control" placeholder="Add Junctions (comma seperated)" id="junstion_edit" cols="30" rows="5" required data-rule-required="true" data-msg-required="This field is required"></textarea>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-warning btn-min-width mr-1 mb-1" id="confirmAction">Update Route</button>
                                <button type="button" class="btn btn-primary btn-min-width mr-1 mb-1" data-dismiss="modal">Cancel</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="AssignLocationsView" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AssignLocationsView"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <h4 class="modal-title w-100 font-weight-bold">Pickup Addresses <span></span></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mx-3">

                    </div>
                    <div class="modal-footer d-flex justify-content-end">
                        <button class="btn btn-grey" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade text-left" id="assign_location" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AssignLocations"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h4 class="modal-title white">Assign Shippers</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <form id="route_location" action="{{route('admin.management.route.assign_location')}}" method="post" novalidate="novalidate">
                            @method('post')
                            {{ csrf_field() }}
                            <input type="text" hidden id="route_id" name="route_id">
                            <input type="text" hidden id="pickup_address_id" name="pickup_address_id">
                            <div class="col-12">
                                <fieldset class="form-group">
                                    <select name="users"  id="users" class="form-control select2" data-rule-required="true" data-msg-required="Shipper is required">
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12">
                                <fieldset class="form-group pickup_address_parent ">
                                    <select name="pickup_address[]"  id="pickup_address" class="form-control select2" data-rule-required="true" data-msg-required="Location is required">

                                    </select>
                                </fieldset>
                            </div>
                            <table class="table table-bordered datatable" id="view_address" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Address</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>

                            <div class="row justify-content-center mt-4">
                                <div class="col-4">
                                    <button id="edit" type="submit" class="btn btn-primary btn-block">Assign Shippers</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">


    <style type="text/css">
        table.dataTable {
            font-size: 12px;
        }
        #view_address_info{
            text-align: center;
        }

        table.dataTable .dataTables_info{
            text-align: center;
        }
        textarea#junction {
            resize: none;
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
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script type="text/javascript">

        $(document).ready(function() {

            $('#city_list').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select City',
                width:'100%',
                allowClear:true,
                dropdownParent: $("#add_route")
            });
            $('#editRouteForm #city_id').select2({
                placeholder:'Select City',
                width:'100%',
                allowClear:true,
                dropdownParent: $("#edit_route_modal")
            });

            $('#editRouteForm #rider_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Rider',
                width:'100%',
                allowClear:true,
                dropdownParent: $("#edit_route_modal")
            });
            $('#rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Rider',
                width:'100%',
                allowClear:true,
                dropdownParent: $("#add_route")
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.v2_pickups.pickup_route.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('City Name');
                            head.push('Route Code');
                            head.push('Rider Trax ID');
                            head.push('Rider Name');
                            head.push('Start Point');
                            head.push('End Point');
                            head.push('Junction');
                            head.push('Added Date/Time');
                            head.push('Status');
                           /* head.push('Route Type');*/


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.city);
                                row.push(values.code);
                                row.push(values.rider_trax_id);
                                row.push(values.rider);
                                row.push(values.start);
                                row.push(values.end);
                                row.push(values.junction);
                                row.push(values.created_at);
                                row.push(values.status);
                               /* row.push(values.route_type);*/

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var table =  $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                @if (session('role_id') == 1 || in_array(93, session('permissions')))

                buttons: [{
                    text: 'Add Route',
                    className: 'btn btn-primary',
                    enabled: true,
                    action: function (e, dt, node, config) {
                        $('#add_route').modal('show');

                    }

                },
                    {
                        extend: 'excel',
                        title: 'Pickup Route',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'],
                @else
                buttons: [{
                    extend: 'excel',
                    title: 'Pickup Routes',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                @endif
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.v2_pickups.pickup_route.list') }}',
                rowId: 'id',
                order: [[8, 'desc']],
                columns: [
                    //{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id',orderable: false, searchable: false, name: 'align-middle serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'city', name: 'cities.name', class: 'align-middle city'},
                    {data: 'code', name: 'routes.code', class: 'align-middle code'},
                    {data: 'rider_trax_id', name: 'riders.trax_id', class: 'align-middle trax_id'},
                    {data: 'rider', name: 'riders.name', class: 'align-middle rider'},
                    {data: 'start', name: 'routes.start', class: 'align-middle start'},
                    {data: 'end', name: 'routes.end', class: 'align-middle end'},
                    {data: 'junction', name: 'routes.junction', class: 'align-middle junction'},
                    {data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
                    {data: 'status', name: 'routes.status', class: 'align-middle status'},
                  /*  {data: 'route_type', name: 'rt.id', class: 'align-middle route_type'},*/
                    {data: 'action', name: 'action', class: 'align-middle action text-center', orderable: false, searchable: false}
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
                        '<option value="0">Inactive</option>' +
                        '<option value="1">Active</option>' +
                        '</select>';
                    var route_type = '<select name="route_type" id="route_type" class="select2 form-control">' +
                        '<option value="1">Pickup</option>' +
                        '<option value="2">Delivery</option>' +
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
                        }else if($(header).is('.route_type')){
                            $(route_type).appendTo($(search))
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
            var view_address = $('#view_address').DataTable({
                dom: '<"d-inline-block"l>tipr',
                ordering:false,
                paging:false,
                searching: false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {name: 'user', class: 'align-middle user form-group'},
                    {name: 'address', class: 'align-middle address form-group'},
                    {name: 'action', class: 'align-middle action'},
                ],

                rowCallback: function(row, data, index) {
                    var info = view_address.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
                initComplete: function() {

                    // this.api().table().columns.adjust();
                }

            });


            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                    var route_id = table.row( $(this).parents('tr') ).data().id;
                    if ($(this).hasClass('update_route')) {
                        $.ajax({
                            url: '{!! route('admin.v2_pickups.pickup_route.edit_ajax') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'route_id': route_id
                            }
                        }).done(function(data){

                            if(data.details.length != 0 ){
                                /*$.each(data.details, function(index, value) {*/
                                    var city_id = data.details.city_id;
                                    var route_code = data.details.code;
                                    var start = data.details.start;
                                    var end = data.details.end;
                                    var rider_id = data.details.rider_id;
                                    var junctions = data.details.junctions;

                                    $('#city_id').val(city_id).trigger('change');
                                    $('#route_code').val(route_code);
                                    $('#start').val(start);
                                    $('#end').val(end);
                                    $('#rider_id').val(rider_id).trigger('change');
                                    $('#junstion_edit').val(junctions);
                                //});
                                $('#edit_route_modal').modal('show');
                                var route = '{!! route('admin.management.route.edit', ':id') !!}';
                                    route = route.replace(':id', route_id);
                                $("#editRouteForm").attr('action', route);

                            }

                        });

                    }
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu  .dropdown-item', function() {

                if ($(this).hasClass('view_location')) {
                    var route_id = table.row( $(this).parents('tr') ).data().id;
                    var rider_trax_id = table.row( $(this).parents('tr') ).data().rider_trax_id;
                    var rider_name = table.row($(this).parents('tr')).data().rider;

                    $.ajax({
                        url: '{!! route('admin.management.route.assign_locations_view') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'route_id': route_id
                        }
                    }).done(function(data){
                        if(data.locations.length != 0){

                            var html = '';
                            html += '<table class="table table-sm datatable text-center">';
                            html += '<thead><tr><th>S No.</th><th><strong>Details</strong></th></tr></thead>';
                            html += '<tbody>';
                            $.each(data.locations, function(index, value) {
                                var ind = index+1;
                                html += '<tr class=""><td>' + ind + '</td>';
                                html += '<td>' + value + '</td>';
                            });
                            html += '</tbody></table>';

                            $('#AssignLocationsView .modal-header h4 span').html(' (' + rider_name + ' - ' + rider_trax_id + ')');
                            $('#AssignLocationsView .modal-body').html(html);
                            $('#AssignLocationsView').modal('show');
                        }
                        else{
                            var error = "No Address Found";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }

                    });
                }
            });

            var rows_count = 0;
            var selected_rows = [];
            var locations = [];

            function add_row(pickup_address_id,pickup_address_location,user) {

                var index = $.inArray(pickup_address_id, locations);
                if (index === -1) {
                    rows_count++;
                    if (rows_count == 1){
                        var remove = '';
                    }else{
                        var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';
                    }

                    view_address.row.add([0,user,pickup_address_location,remove]).node().id = pickup_address_id;
                    locations.push(pickup_address_id);
                    view_address.draw(true);
                }
                else{
                    var error = "Address already exists";
                    toastr.error(error, 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }

                $('#edit').attr('disabled', false);
            }

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {

                var route_id = $(this).parents('tr').attr('id');

                $('#route_id').val(route_id);

                if ($(this).hasClass('assign_location')) {
                    $('#assign_location').modal('show');

                }
            });

            $('#users').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Shipper",
                dropdownParent:$('#route_location')
            }).bind('select2:select', function () {
                if(this.value){
                    $.ajax({
                        url: '{!! route('admin.management.route.user_address') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'user_id': this.value,
                        }
                    }).done(function(data){

                        if (data.status == 1) {
                            $('#pickup_address').empty().trigger('change');
                            $.each(data.addresses, function(key,value) {
                                var newOption = new Option(value.pickup_address,value.id, false, false);
                                $('#pickup_address').append(newOption).trigger('change');

                            });
                            $('#pickup_address').val('').trigger('change');
                        }
                        else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                            $('#assign_location').modal('hide');

                        }
                    });
                    //$('#pickup_address').empty().trigger('change');
                }
            });
            $('#pickup_address').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Pickup Address",
                allowClear:true,
                dropdownParent:$('#route_location')
            }).bind('select2:select',function(){
                var address_id = parseInt($(this).val());
                var address = $(this).find(':selected').text();
                var user = $( "#users option:selected" ).text();
                add_row(address_id,address,user);

            });
            $('body').on('click', 'a.remove_row',function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, locations);

                if (index !== -1) {
                    locations.splice(index, 1);
                }
                $('#route_location #pickup_address_id').val(locations);
                view_address.row( $(this).parents('tr') ).remove().draw();
            });


            $( "#route_location" ).validate({

                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    $('#route_location #pickup_address_id').val(locations);
                    swal({
                        title: 'Please Wait!',
                        text: 'Location is being saved!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });

        $( "#editRouteForm" ).validate({

            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {

                $(form).find('button[type=submit]').attr('disabled', 'disabled');
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

        $( "#addRouteForm" ).validate({

            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {

                $(form).find('button[type=submit]').attr('disabled', 'disabled');
                swal({
                    title: 'Please Wait!',
                    text: 'Route is being Saved!',
                    icon: 'info',
                    buttons: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false
                });

                form.submit();
            }
        });

        $('body').on('click','.deactivate',function (e) {
            var id = $(this).data('target-id');
            var rel = $(this).attr('rel');

            $('#active_route_form #cid').val(id);
            $('#active_route_form #cstatus').val(rel);
            if(rel == 'routeInactive'){
                var atext = "Select Yes to Deactive this Route!";
            }else{
                var atext = "Select Yes to active this Route!";
            }
            swal({
                title: 'Are You Sure?',
                text: atext,
                icon: 'warning',
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
                    $('#active_route_form').submit();
                }
            });

        });

        $('#assign_location').on('hide.bs.modal', function () {
            //$('#pickup_address').val('').trigger('change');
            $('#pickup_address').empty().trigger('change');
            $('#users').val('').trigger('change');
            var view_address = $('#view_address').DataTable();
            view_address.clear();
            locations = [];
            view_address.draw();
            selected_rows = [];
            rows_count = 0;
            // $('#return_note_image_view_table tbody').html('');
        });
    });

    </script>

@endsection