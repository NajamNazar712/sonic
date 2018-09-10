
@extends('admin.layout.master')

@section('content')
    <h1 class="mb-1">
        Packaging Material Requests
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="container justify-content-center pb-2 text-center">
                    <div class="row">
                        <div class="col-3"><h4>Small Flyers: <u id="sm_flyers_title">{{$packaging->small_flyers}}</u></h4></div>
                        <div class="col-3"><h4>Medium Flyers: <u id="md_flyers_title">{{$packaging->medium_flyers}}</u></h4></div>
                        <div class="col-3"><h4>Large Flyers: <u id="lg_flyers_title">{{$packaging->large_flyers}}</u></h4></div>
                        <div class="col-3"><h4>Boxes: <u id="box_title">{{$packaging->boxes}}</u></h4></div>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Requested Date/Time</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">Small Flyers</th>
                        <th class="border-primary border-darken-1">Medium Flyers</th>
                        <th class="border-primary border-darken-1">Large Flyers</th>
                        <th class="border-primary border-darken-1">Boxes</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Payment Mode</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>



@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">




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
    {{--<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>--}}
    {{--<script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>--}}
{{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
{{--    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
{{--    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>--}}
    {{--<script src="{{asset('app-assets/vendors/js/ui/perfect-scrollbar.jquery.min.js')}}" type="text/javascript"></script>--}}



    <script type="text/javascript">
        $(document).ready(function () {


            var table = $('#datatable').DataTable({
                // "scrollX": true,
                dom: 'ltipr',
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.packaging.requests.list') }}',
                rowId: 'request_id',
                order: [[2, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'created_at', name: 'packaging_material_requests.created_at', class: 'align-middle created_at'},
                    {data: 'city', name: 'ct.name', class: 'align-middle city'},
                    {data: 'small_flyers', name: 'packaging_material_requests.small_flyers', class: 'align-middle small_flyers'},
                    {data: 'medium_flyers', name: 'packaging_material_requests.medium_flyers', class: 'align-middle medium_flyers'},
                    {data: 'large_flyers', name: 'packaging_material_requests.large_flyers', class: 'align-middle large_flyers'},
                    {data: 'boxes', name: 'packaging_material_requests.boxes', class: 'align-middle boxes'},
                    {data: 'address', name: 'packaging_material_requests.address', class: 'align-middle address'},
                    {data: 'mode', name: 'ppm.mode', class: 'align-middle mode'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'action', name: 'action', class: 'align-middle action',orderable: false, searchable: false}

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
            //dispatch
            $('body').on('click','.dispatch',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url: '{!! route('admin.packaging.requests.dispatch') !!}',
                    method: 'POST',
                    data: {
                        'id': request_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {

                    if(data.status === 1){
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        setTimeout(function(){
                            window.location.reload();
                        },2000);
                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                    }
                });
            });

        });

    </script>
@endsection