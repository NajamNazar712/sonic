@extends('admin.layout.master')


@section('content')

    <h1>Route Management</h1>

    <section>

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <span class="font-large-1 card-title">Routes List</span>
                        {{--<button type="button" rel="addroute" class="btn btn-primary btn-min-width mr-1 mb-1 pull-right" data-target="#addRoute" data-toggle="modal">Add Route</button>--}}

                        <div class="mt-1">
                            @include('admin.inc.messages')
                        </div>
                    </div>


                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
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
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <style>
        table.dataTable {
            font-size: 12px;
        }

        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
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
        table.dataTable tbody tr td.junction{
            /*word-break: break-all;*/
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
    {{--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBu-916DdpKAjTmJNIgngS6HL_kDIKU0aU&callback=myMap"></script>--}}
    {{--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBMo9kqvMhqVAe_GCXZXOfzfAZ_oeBapkQ"></script>--}}
    {{--<script src = "https://maps.googleapis.com/maps/api/js"></script>--}}
    <script type="text/javascript">
        // document.addEventListener('DOMContentLoaded', function () {
        //     if (document.querySelectorAll('#map').length > 0)
        //     {
        //         if (document.querySelector('html').lang)
        //             lang = document.querySelector('html').lang;
        //         else
        //             lang = 'en';
        //
        //         var js_file = document.createElement('script');
        //         js_file.type = 'text/javascript';
        //         js_file.src = 'https://maps.googleapis.com/maps/api/js?callback=initMap&signed_in=true&key=AIzaSyBu-916DdpKAjTmJNIgngS6HL_kDIKU0aU&language=' + lang;
        //         document.getElementsByTagName('head')[0].appendChild(js_file);
        //     }
        // });
        // var map;
        //
        // function initMap() {
        //     map = new google.maps.Map(document.getElementById('map'), {
        //         center: {lat: -34.397, lng: 150.644},
        //         zoom: 8
        //     });
        // }
        // setTimeout(function () {
        //     initMap();
        // },5000);
        $(document).ready(function() {

            var table =  $('.datatable').DataTable({
                @if (session('role_id') == 1 || in_array(93, session('permissions')))
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    buttons: [{
                        text: 'Add Route',
                        className: 'btn btn-primary',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#addRoute').modal('show');

                        }

                    }],
                @else
                    dom: 'ltipr',
                @endif
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                stateSave: true,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                order: [[6, 'desc']],
                ajax: '{{ route('admin.management.route.ajax') }}',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'city', name: 'cities.name', class: 'city'},
                    {data: 'code', name: 'routes.code', class: 'code'},
                    {data: 'start', name: 'routes.start', class: 'start'},
                    {data: 'end', name: 'routes.end', class: 'end'},
                    {data: 'junction', name: 'routes.junction', class: 'junction'},
                    {data: 'created_at', name: 'created_at', class: 'created_at'},
                    {data: 'status', name: 'status', class: 'status'},
                    {data: 'action', name: 'action', class: 'action text-center', orderable: false, searchable: false}
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

            $('.routeConfirmation #cid').val(id);
            $('.routeConfirmation #cstatus').val(rel);

        });
    });
    </script>

@endsection