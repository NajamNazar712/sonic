@extends('admin.layout.master')

@section('content')
    <h1>Rider Management</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <span class="font-large-1 card-title">Riders List</span>
                        {{--<button type="button" rel="addroute" class="btn btn-primary btn-min-width mr-1 mb-1 pull-right" data-target="#addRider" data-toggle="modal">Add Rider</button>--}}

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
                                    <th>City</th>
                                    <th>Name</th>
                                    <th>Phone No</th>
                                    <th>CNIC</th>
                                    <th>Address</th>
                                    <th>Route</th>
                                    <th>Category</th>
                                    <th>Added On</th>
                                    <th>Status</th>
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
        /*table.dataTable tbody tr td.junction{*/
            /*word-wrap: break-word;*/
            /*background: #606060;*/
        /*}*/

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
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

    <script type="text/javascript">
        $(document).ready(function() {
            var table =  $('.datatable').DataTable({
                @if (session('role_id') == 1 || in_array(97, session('permissions')))
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    buttons: [{
                        text: 'Add Rider',
                        className: 'btn btn-primary',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#addRider').modal('show');

                        }

                    }],
                @else
                    dom: 'ltipr',
                @endif
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,

                ajax: '{{ route('admin.management.rider.ajax') }}',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'city', name: 'cities.name', class: 'city'},
                    {data: 'rider', name: 'riders.name', class: 'name'},
                    {data: 'phone', name: 'riders.phone', class: 'phone'},
                    {data: 'cnic', name: 'riders.cnic', class: 'cnic'},
                    {data: 'address', name: 'riders.address', class: 'address'},
                    {data: 'route', name: 'route', class: 'route'},
                    {data: 'category', name: 'rider_categories.name', class: 'category'},
                    {data: 'created_at', name: 'created_at', class: 'created_at'},
                    {data: 'status', name: 'status', class: 'status'},
                    {data: 'action', name: 'action', class: 'text-center action', orderable: false, searchable: false}
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


            $("#addRider").on("show.bs.modal", function(e) {
                $.get( "/admin/management/rider/add", function( data ) {
                    $("#addRiderDiv").html(data);
                });
            });
            $("#editRider").on("show.bs.modal", function(e) {

                var id = $(e.relatedTarget).data('target-id');

                $.get( "/admin/management/rider/"+id+"/edit", function( data ) {
                    $("#editRiderDiv").html(data);
                });

            });

            $('body').on('click','.deactivate',function (e) {
                var id = $(this).data('target-id');
                var rel = $(this).attr('rel');

                $('.riderConfirmation #cid').val(id);
                $('.riderConfirmation #cstatus').val(rel);

            });
        });
    </script>

@endsection