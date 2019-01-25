
@extends('admin.layout.master')
@section('title','Quick Tracking')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <h1 class="mb-1">
                    Quick Tracking
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form action="#" id="quick_tracking_form">
                                <div class="row justify-content-center mb-2">
                                    <div class="col-3">
                                        <fieldset>
                                            <input type="text" class="form-control" placeholder="Scan Tracking Number" id="scan_tracking">
                                        </fieldset>
                                    </div>
                                </div>
                            </form>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Reason</th>
                                    <th class="border-primary border-darken-1">Remarks</th>
                                    <th class="border-primary border-darken-1">Status Date</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                </tr>
                                </thead>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                // scrollX: true,
                // lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                // pageLength: 50,
                // pagingType: 'full_numbers',
                paging:false,
                ordering:[0, 'desc'],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'tracking_number', class: 'align-middle tracking_numbers', orderable: false},
                    {name: 'current_status', class: 'align-middle current_status', orderable: false},
                    {name: 'reason', class: 'align-middle reason', orderable: false},
                    {name: 'remarks', class: 'align-middle remarks', orderable: false},
                    {name: 'current_status_date', class: 'align-middle current_status_date', orderable: false},
                    {name: 'origin', class: 'align-middle origin', orderable: false},
                    {name: 'destination', class: 'align-middle destination', orderable: false}
                ],
                rowCallback: function(row, data, index) {
                    // var info = table.page.info();

                    // $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    // var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());
                    //
                    // var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    // var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    // var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    // this.api().table().columns.adjust();
                }
            });

            $('#scan_tracking').on('change',function() {
                $(this).val($(this).val().trim());
            });
            $('input#scan_tracking').focus();
            $('#quick_tracking_form').on('submit',function (e) {
                e.preventDefault();
                var scan = $('#scan_tracking');
                var tracking = scan.val();


                if (tracking != '') {
                    scan.attr('disabled', true);

                    if(table.row().count() == 0) {
                        $.ajax({
                            url:'{{route('admin.quick_tracking.info')}}',
                            type:'POST',
                            data: {
                                'tracking':tracking,
                                '_token': '{!! csrf_token() !!}'
                            }
                        }).done(function (data) {

                            if(data.status == 0){
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }else{
                                var rowNo = table.rows().count();

                                table.row.add([rowNo+1,parseInt(data.details.tracking_number),data.details.status,data.details.reason,data.details.remarks,data.details.current_status_date,data.details.origin,data.details.destination]);
                                table.draw(false);
                            }

                            scan.val('');
                            scan.attr('disabled', false);
                            scan.focus();
                        });
                    } else {
                        if(table.columns('.tracking_numbers').data().eq(0).indexOf(parseInt(tracking)) === -1){
                            $.ajax({
                                url:'{{route('admin.quick_tracking.info')}}',
                                type:'POST',
                                data: {
                                    'tracking':tracking,
                                    '_token':'{!! csrf_token() !!}'
                                }
                            }).done(function (data) {
                                if(data.status == 0){
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }else{
                                    var rowNo = table.rows().count();

                                    table.row.add([rowNo+1,parseInt(data.details.tracking_number),data.details.status,data.details.reason,data.details.remarks,data.details.current_status_date,data.details.origin,data.details.destination]);
                                    table.draw(false);
                                    table.order([0, 'desc']).draw();
                                }

                                scan.val('');
                                scan.attr('disabled', false);
                                scan.focus();
                            });
                        }else{
                            var error = 'Tracking Number already scanned!';
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            scan.val('');
                            scan.attr('disabled', false);
                            scan.focus();
                        }
                    }
                }
            });
        });
    </script>
@endsection