@extends('admin.layout.master')

@section('title', 'Route Management')

@section('content')

    <h1>Route Management</h1>

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
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">City Name</th>
                                    <th class="border-primary border-darken-1">Route Code</th>
                                    <th class="border-primary border-darken-1">Rider Name</th>
                                    <th class="border-primary border-darken-1">Start Point</th>
                                    <th class="border-primary border-darken-1">End Point</th>
                                    <th class="border-primary border-darken-1">Junction</th>
                                    <th class="border-primary border-darken-1">Added Date/Time</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Route Types</th>
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
{{--        <div class="modal fade" id="AssignLocationsView" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AssignLocationsView"--}}
{{--             aria-hidden="true">--}}
{{--            <div class="modal-dialog modal-lg" role="document">--}}
{{--                <div class="modal-content">--}}
{{--                    <div class="modal-header text-center">--}}
{{--                        <h4 class="modal-title w-100 font-weight-bold">View Addresses</h4>--}}
{{--                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                            <span aria-hidden="true">&times;</span>--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                    <div class="modal-body mx-3">--}}

{{--                    </div>--}}
{{--                    <div class="modal-footer d-flex justify-content-end">--}}
{{--                        <button class="btn btn-grey" data-dismiss="modal">Close</button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <div class="modal fade text-left" id="assign_location" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AssignLocations"--}}
{{--             aria-hidden="true">--}}
{{--            <div class="modal-dialog modal-lg" role="document">--}}
{{--                <div class="modal-content">--}}
{{--                    <div class="modal-header bg-primary white">--}}
{{--                        <h4 class="modal-title white">Assign Shippers</h4>--}}
{{--                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                            <span aria-hidden="true">&times;</span>--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                    <div class="modal-body text-center">--}}
{{--                        <form id="route_location" action="{{route('admin.management.route.assign_location')}}" method="post" novalidate="novalidate">--}}
{{--                            @method('post')--}}
{{--                            {{ csrf_field() }}--}}
{{--                            <input type="text" hidden id="route_id" name="route_id">--}}
{{--                            <div class="">--}}
{{--                                <fieldset class="col-12 form-group">--}}
{{--                                    <select name="pickup_address[]"  id="pickup_address" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="Location is required">--}}
{{--                                        @foreach($users as $user)--}}
{{--                                            <option value="{{$user->address_id}}">{{$user->name}} - {{$user->pickup_address}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </fieldset>--}}
{{--                            </div>--}}

{{--                            <div class="row justify-content-center mt-4">--}}
{{--                                <div class="col-4">--}}
{{--                                    <button type="submit" class="btn btn-primary btn-block">Assign Shippers</button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </form>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
    </section>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script type="text/javascript">

        $(document).ready(function() {
            // $('#pickup_address').prepend('<option value="" selected="selected"></option>').select2({
            //     width:'100%',
            //     placeholder:"Pickup Addresses",
            //     dropdownParent: $("#assign_location")
            //     //allowClear:true,
            // });
           /* $('#pickup_address').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Search Pickup Address'
            });*/
            var selected_rows = [];
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.management.route.ajax') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('City Name');
                            head.push('Route Code');
                            head.push('Rider Name');
                            head.push('Start Point');
                            head.push('End Point');
                            head.push('Junction');
                            head.push('Added Date/Time');
                            head.push('Status');
                            head.push('Route Type');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.city);
                                row.push(values.code);
                                row.push(values.rider);
                                row.push(values.start);
                                row.push(values.end);
                                row.push(values.junction);
                                row.push(values.created_at);
                                row.push(values.status);
                                row.push(values.route_type);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var table =  $('.datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                @if (session('role_id') == 1 || in_array(93, session('permissions')))

                    buttons: [{
                        text: 'Add Route',
                        className: 'btn btn-primary',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#addRoute').modal('show');

                        }

                    },{
                    text: '<i class="la la-cogs"></i> Set as Pickup Route',
                    className: 'btn btn-primary pickup',
                    enabled:false,
                    action: function (e, dt, node, config) {

                        $('input:hidden[name=id]').val(selected_rows);

                        if(selected_rows.length === 0){
                            table.button('.pickup').disable();
                            return false;
                        }
                        else if(selected_rows !== ''){
                            swal({
                                title: 'Are You Sure?',
                                text: 'Select Yes to set as pickup route !',
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
                                    $.ajax({
                                        url: '{!! route('admin.management.route.set_pickup_route') !!}',
                                        method: 'POST',
                                        data: {
                                            '_token': '{{ csrf_token() }}',
                                            'route_id': selected_rows
                                        }
                                    }).done(function(data){
                                        if(data.status){
                                            table.rows().deselect();
                                            selected_rows = [];
                                            table.button('.pickup').disable();
                                            table.draw(true);
                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                        }

                                    });
                                }
                            });

                        }else{
                            var error = 'Route Not Found, Please Try again!';
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            table.button('.pickup').disable();
                        }
                    }
                },{
                    extend: 'selectAll',
                    text: 'Select All',
                    className: 'select_all',
                    action : function(e) {
                        e.preventDefault();

                        table.rows().nodes().each(function(index) {
                            var row = table.row(index);

                            if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                row.select();

                                id = parseInt(row.id());

                                var index = $.inArray(id, selected_rows);

                                if (index === -1) {
                                    selected_rows.push(id);
                                }

                                table.button('.pickup').enable();
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

                            if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                row.deselect();

                                id = parseInt(row.id());

                                var index = $.inArray(id, selected_rows);

                                if (index !== -1) {
                                    selected_rows.splice(index, 1);
                                }

                                if (selected_rows.length == 0) {
                                    table.button('.pickup').disable();
                                }
                            }
                        });
                    }
                },
                    {
                        extend: 'excel',
                        title: 'Route Management',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'],
                @else
                buttons: [{
                    extend: 'excel',
                    title: 'Route Management',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                @endif
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
                ajax: '{{ route('admin.management.route.ajax') }}',
                rowId: 'id',
                order: [[8, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'align-middle serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'city', name: 'cities.name', class: 'align-middle city'},
                    {data: 'code', name: 'routes.code', class: 'align-middle code'},
                    {data: 'rider', name: 'riders.name', class: 'align-middle rider'},
                    {data: 'start', name: 'routes.start', class: 'align-middle start'},
                    {data: 'end', name: 'routes.end', class: 'align-middle end'},
                    {data: 'junction', name: 'routes.junction', class: 'align-middle junction'},
                    {data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
                    {data: 'status', name: 'routes.status', class: 'align-middle status'},
                    {data: 'route_type', name: 'rt.id', class: 'align-middle route_type'},
                    {data: 'action', name: 'action', class: 'align-middle action text-center', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {

                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                    if (data.route_type == 'Delivery') {
                        $('td:eq(0)', row).addClass('select-checkbox');

                        if ($.inArray(data.id, selected_rows) !== -1) {
                            table.row(row).select();
                        }
                    }

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

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.select') ) {
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
                    $("#route_type").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Route Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
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
                    table.button('.pickup').enable();
                }
                else {
                    table.button('.pickup').disable();
                }
            });


            {{--$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {--}}

            {{--    var route_id = $(this).parents('tr').attr('id');--}}
            {{--    if(route_id){--}}

            {{--        $('#route_id').val(route_id);--}}

            {{--        if ($(this).hasClass('assign_location')) {--}}
            {{--            blockPagePermanently();--}}
            {{--            $.ajax({--}}
            {{--                url: '{!! route('admin.management.route.view_assign_location') !!}',--}}
            {{--                method: 'POST',--}}
            {{--                data: {--}}
            {{--                    '_token': '{{ csrf_token() }}',--}}
            {{--                    'route_id': route_id--}}
            {{--                }--}}
            {{--            }).done(function(data){--}}
            {{--                $('#pickup_address').val('All').trigger('change');--}}
            {{--                if(data.pickup_address_ids.length != 0 ){--}}
            {{--                    $('#pickup_address').val(data.pickup_address_ids).trigger('change');--}}
            {{--                }--}}
            {{--                $('#assign_location').modal('show');--}}
            {{--                UnblockPagePermanently();--}}
            {{--            });--}}
            {{--        }--}}
            {{--    }--}}
            {{--});--}}

            {{--$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu  .dropdown-item', function() {--}}

            {{--    if ($(this).hasClass('view_location')) {--}}
            {{--        var route_id =$(this).parents('tr').attr('id');--}}
            {{--        if(route_id){--}}
            {{--            blockPagePermanently();--}}
            {{--            $.ajax({--}}
            {{--                url: '{!! route('admin.management.route.assign_locations_view') !!}',--}}
            {{--                method: 'POST',--}}
            {{--                data: {--}}
            {{--                    '_token': '{{ csrf_token() }}',--}}
            {{--                    'route_id': route_id--}}
            {{--                }--}}
            {{--            }).done(function(data){--}}
            {{--                if(data.locations.length != 0){--}}
            {{--                    var html = '';--}}
            {{--                    html += '<table class="table table-sm datatable text-center">';--}}
            {{--                    html += '<thead><tr><th>S No.</th><th><strong>Addresses</strong></th></tr></thead>';--}}
            {{--                    html += '<tbody>';--}}
            {{--                    $.each(data.locations, function(index, value) {--}}
            {{--                        var ind = index+1;--}}
            {{--                        html += '<tr class=""><td>' + ind + '</td>';--}}
            {{--                        html += '<td>' + value + '</td>';--}}
            {{--                    });--}}
            {{--                    html += '</tbody></table>';--}}

            {{--                    $('#AssignLocationsView .modal-body').html(html);--}}
            {{--                    $('#AssignLocationsView').modal('show');--}}
            {{--                }--}}
            {{--                UnblockPagePermanently();--}}
            {{--            });--}}
            {{--        }--}}

            {{--    }--}}
            {{--});--}}

        });
        $( "#route_location" ).validate({

            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {

                $(form).find('button[type=submit]').attr('disabled', 'disabled');
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

        $("#addRoute").on("show.bs.modal", function(e) {
                $.get( "/admin/management/route/add", function( data ) {
                    $("#addRouteDiv").html(data);
                });
        });
        $("#editRoute").on("show.bs.modal", function(e) {

            var id = $(e.relatedTarget).data('target-id');

            $.get( "/admin/management/route/"+id+"/edit", function( data ) {
                $("#editRouteDiv").html(data);
            });

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
    </script>

@endsection