
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
                                    <div class="col-3">
                                        <fieldset>
                                            <div class="float-left">
                                                <input name="switch" type="checkbox"  class="switch single_multiple_switch" data-size="md" data-off-label="Multiple" data-on-label="Single" checked/>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </form>

                            <div id="single_div" class="d-none">
                            <div class="row">
                                {{--<div class="col-2"><div id="tracking">Tracking Number</div><div>202202000116</div></div>--}}
                                <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Tracking Number</h4>
                                                <p class="card-text track">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                                <div class="col-6"><div class="card text-center" id="status_card">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title">Status</h4>
                                                <p class="card-text status">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                                <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Status Date</h4>
                                                <p class="card-text date">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                                <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Reason</h4>
                                                <p class="card-text reason">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                                <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Remarks</h4>
                                                <p class="card-text remarks">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                                <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Origin</h4>
                                                <p class="card-text origin">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                                <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Destination</h4>
                                                <p class="card-text destination">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                            </div>
                            </div>
                            <div id="multiple_div" class="d-none">
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
        #single_div p.status{
            font-weight: bold;
        }
        #single_div{
            font-size: 20px;
        }
        #single_div h4{
            font-weight: bolder;
            font-size: 18px;
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
        .datatable tbody tr{
            font-size: 18px;
            font-weight: bold;
        }
        .yellowClass{
            background-color: #86cd7c;
        }
        .greenClass{
            background-color: green;
        }
        .redClass{
            background-color: orangered;
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
            var table;
            var selection = true;
            $('.single_multiple_switch').on('change',function(){
                var single_multiple_switch = document.querySelector('input.single_multiple_switch');
                if (single_multiple_switch.checked === true) {
                   selection = true;
                   $('#multiple_div').addClass('d-none');
                   // $('#single_div').removeClass('d-none');
                    destroyDatatable();
                } else if (single_multiple_switch.checked === false) {
                    selection = false;
                    $('#multiple_div').removeClass('d-none');
                    $('#single_div').addClass('d-none');
                    // table.clear();
                    init();

                }
            });

            function destroyDatatable() {
                table.clear();
                table.destroy();
            }
            function init() {
                table = $('#datatable').DataTable({
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    buttons:[{
                        extend: 'excel',
                        title: 'Quick Tracking',
                        className: 'btn btn-primary mb-1',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
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
                        var status = parseInt($(row).attr('id'));
                        if(status === 13){
                            $(row).addClass('greenClass');
                        }else if(status === 12 || status === 52){
                            $(row).addClass('yellowClass');
                        }else if(status === 20){
                            $(row).addClass('redClass');
                        }
                    },
                    initComplete: function() {

                    }
                });
            }

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
                    if(selection === false){
                        $('#multiple_div').removeClass('d-none');
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

                                    table.row.add([rowNo+1,parseInt(data.details.tracking_number),data.details.status,data.details.reason,data.details.remarks,data.details.current_status_date,data.details.origin,data.details.destination]).node().id = data.details.status_id;
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

                                        table.row.add([rowNo+1,parseInt(data.details.tracking_number),data.details.status,data.details.reason,data.details.remarks,data.details.current_status_date,data.details.origin,data.details.destination]).node().id = data.details.status_id;
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
                    else{
                        $.ajax({
                            url:'{{route('admin.quick_tracking.info')}}',
                            type:'POST',
                            data: {
                                'tracking':tracking,
                                '_token': '{!! csrf_token() !!}'
                            }
                        }).done(function (data) {

                            if(data.status == 0){
                                $('#single_div').addClass('d-none');
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }else{
                                $('#single_div').removeClass('d-none');
                                if($('#status_card').hasClass('greenClass') || $('#status_card').hasClass('redClass') || $('#status_card').hasClass('yellowClass')){
                                    $('#status_card').removeClass('greenClass');
                                    $('#status_card').removeClass('redClass');
                                    $('#status_card').removeClass('yellowClass');
                                }


                                $('#single_div p.track').text(data.details.tracking_number);
                                $('#single_div p.status').text(data.details.status);
                                $('#single_div p.origin').text(data.details.origin);
                                $('#single_div p.destination').text(data.details.destination);
                                if(data.details.reason == null){
                                    $('#single_div p.reason').text('No Reason');
                                }else{
                                    $('#single_div p.reason').text(data.details.reason);
                                }
                                if(data.details.remarks == null){
                                    $('#single_div p.remarks').text('No Remarks');
                                }else{
                                    $('#single_div p.remarks').text(data.details.remarks);
                                }
                                $('#single_div p.date').text(data.details.current_status_date);
                                if(data.details.status_id == 13){
                                    $('#status_card').addClass('greenClass');
                                }else if(data.details.status_id == 12 || data.details.status_id == 52){
                                    $('#status_card').addClass('yellowClass');
                                }else if(data.details.status_id == 20){
                                    $('#status_card').addClass('redClass');
                                }

                                // var rowNo = table.rows().count();
                                //
                                // table.row.add([rowNo+1,parseInt(data.details.tracking_number),data.details.status,data.details.reason,data.details.remarks,data.details.current_status_date,data.details.origin,data.details.destination]).node().id = data.details.status_id;
                                // table.draw(false);
                            }

                            scan.val('');
                            scan.attr('disabled', false);
                            scan.focus();
                        });

                    }
                }
            });
        });
    </script>
@endsection