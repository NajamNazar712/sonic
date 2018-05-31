@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

@endsection
@section('content')
    <h1>Route Management</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <span class="font-large-1 card-title">Routes List</span>
                        <button type="button" rel="addroute" class="btn btn-primary btn-min-width mr-1 mb-1 pull-right" data-target="#addRoute" data-toggle="modal">Add Route</button>

                        <div class="mt-1">
                            @include('admin.inc.messages')
                        </div>
                    </div>


                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered datatable" id="datatable">
                                <thead>
                                <tr>
                                    <th>S No.</th>
                                    <th>City Name</th>
                                    <th>Route Code</th>
                                    <th>Start Point</th>
                                    <th>End Point</th>
                                    <th>Junction</th>
                                    <th>Added Date/Time</th>
                                    <th>Status</th>
                                    {{--<th>Services Available</th>--}}
                                    <th>Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var t =  $('.datatable').DataTable({
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]],
                dom: 'ltipr',
                fixedHeader: {
                    header: true,
                    headerOffset: $('.header-navbar').height()
                },
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                stateSave: true,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,

                ajax: '{{ route('admin.management.route.ajax') }}',
                columns: [
                    {data:'', defaultContent:''},
                    {data: 'name', name: 'name', class: 'city'},
                    {data: 'code', name: 'code', class: 'code'},
                    {data: 'start', name: 'start', class: 'start'},
                    {data: 'end', name: 'end', class: 'end'},
                    {data: 'junction', name: 'junction', class: 'junction'},
                    {data: 'created_at', name: 'created_at', class: 'created_at'},
                    {data: 'status', name: 'status', class: 'status'},
                    {data: 'action', name: 'action', class: 'action', orderable: false, searchable: false}
                ],

                initComplete: function() {
                    var search = $('<tr role="row" class="search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:0;"></td>';
                    var input = '<input type="text" placeholder="Search" style="width:100%;" />';
                    var select = '<select style="width:100%;"><option value=""></option></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.action')) {
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('keyup change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                }
            });
            if (t.data().length != 0) {
                t.on('order.dt search.dt', function () {
                    t.column(0, {search: 'false', order: 'applied'}).nodes().each(function (cell, i) {
                        cell.innerHTML = i + 1;
                        t.cell(cell).invalidate('dom');
                    });
                }).draw();
            }


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

            $('.routeConfirmation #cid').val(id);
            $('.routeConfirmation #cstatus').val(rel);

        });
    });
    </script>

@endsection