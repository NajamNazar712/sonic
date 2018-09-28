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
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">City Name</th>
                                    <th class="border-primary border-darken-1">Route Code</th>
                                    <th class="border-primary border-darken-1">Start Point</th>
                                    <th class="border-primary border-darken-1">End Point</th>
                                    <th class="border-primary border-darken-1">Junction</th>
                                    <th class="border-primary border-darken-1">Added Date/Time</th>
                                    <th class="border-primary border-darken-1">Status</th>
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
    </section>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script type="text/javascript">

        $(document).ready(function() {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.management.route.ajax') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('City Name');
                            head.push('Route Code');
                            head.push('Start Point');
                            head.push('End Point');
                            head.push('Junction');
                            head.push('Added Date/Time');
                            head.push('Status');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.city);
                                row.push(values.code);
                                row.push(values.start);
                                row.push(values.end);
                                row.push(values.junction);
                                row.push(values.created_at);
                                row.push(values.status);

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

                    },
                    {
                        extend: 'excel',
                        title: 'Route Management',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
                @else
                buttons: [{
                    extend: 'excel',
                    title: 'Route Management',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                }],
                @endif
                scrollX: true, scrollY: '350px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.management.route.ajax') }}',
                order: [[6, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'align-middle serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'city', name: 'cities.name', class: 'align-middle city'},
                    {data: 'code', name: 'routes.code', class: 'align-middle code'},
                    {data: 'start', name: 'routes.start', class: 'align-middle start'},
                    {data: 'end', name: 'routes.end', class: 'align-middle end'},
                    {data: 'junction', name: 'routes.junction', class: 'align-middle junction'},
                    {data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
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
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
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
    });
    </script>

@endsection