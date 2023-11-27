
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
                            <div>
                                <button class="btn btn-primary d-none" id="remarks_btn">Add Remarks</button>
                            </div>
                            <form action="#" id="quick_tracking_form">
                                <div class="row justify-content-center mb-2">
                                    <div class="row">
                                        <div class="col-6 btn_scn">
                                          <label class="mr-10 font-medium-3"><b>Scan By Bags</b></label>
                                          <input type="checkbox" name="scan_btn" id="scan_btn" class="switchery scan_btn" data-size="sm" data-switchery="true">
                                        </div>
                                      
                                        <div class="col-6 scan_tracking">
                                          <fieldset>
                                            <input type="text" class="form-control" placeholder="Scan Tracking Number" id="scan_tracking">
                                          </fieldset>

                                            <fieldset>
                                              <input type="text" class="form-control" placeholder="Scan Bag Number" id="scan_bag">
                                            </fieldset>
                                        </div>
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
                                <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Tracking Number</h4>
                                                <p class="card-text track">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                                <div class="col-3"><div class="card text-center" id="status_card">
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
                                                    <h4 class="card-title success">Case Nature ID</h4>
                                                    <p class="card-text case_nature">No Data</p>
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
                                    </div>
                                </div>
                                    
                                    
                            </div>
                                <div class="row justify-content-center">
                                    <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">COD Amount</h4>
                                                <p class="card-text amount">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                                    <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Shipper</h4>
                                                <p class="card-text shipper">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                                    <div class="col-3"><div class="card text-center">
                                            <div class="card-content">
                                                <div class="card-body">
                                                    <h4 class="card-title success">Consignee Name</h4>
                                                    <p class="card-text consignee_name">No Data</p>
                                                </div>
                                            </div>
                                        </div></div>
                                    <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Consignee Address</h4>
                                                <p class="card-text consignee_address">No Data</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    </div>
                            </div>

                            <div id="multiple_div" class="d-none">
                                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Tracking Number</th>
                                        <th class="border-primary border-darken-1">Delivery Note ID</th>
                                        <th class="border-primary border-darken-1">Case Nature ID</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1">Reason</th>
                                        <th class="border-primary border-darken-1">Remarks</th>
                                        <th class="border-primary border-darken-1">Status Date</th>
                                        <th class="border-primary border-darken-1">Origin</th>
                                        <th class="border-primary border-darken-1">Destination</th>
                                        <th class="border-primary border-darken-1">Amount</th>
                                        <th class="border-primary border-darken-1">Shipper Name</th>
                                        <th class="border-primary border-darken-1">Consignee Name</th>
                                        <th class="border-primary border-darken-1">Consignee Address</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>

                
                        <div class="modal fade" id="AddRemarksModal" data-backdrop="static" role="dialog" aria-labelledby="AddRemarksModal"
                            aria-hidden="true">
                            <div class="modal-dialog modal-md" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Add Remarks</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <form method="post" id="add_remarks_form" action="{{ route('admin.quick_tracking.update_remarks') }}"
                                            class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">
                                            @csrf
                                            <input type="hidden" id="tracking_numbers" name="tracking_numbers">
                                            <div class="form-group ml-1">
                                                <textarea name="add_remark" id="add_remark" class="form-control" rows="4"
                                                data-rule-required="true" data-msg-required="Remarks is required"
                                                placeholder="Add Remarks*"></textarea>
                                            </div>
                                            <div class="form-group ml-1">
                                                <button type="submit" name="add" class="btn btn-primary">Add</button>
                                                <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div id="bag_single_div" class="d-none">
                                <div class="row">
                                <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Bag Number</h4>
                                                <p class="card-text bag">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                                <div class="col-3"><div class="card text-center" id="status_card">
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
                                <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title">Bag status</h4>
                                                <p class="card-text bstatus">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                                <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Bag type</h4>
                                                <p class="card-text btype">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                                <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Manifest ID</h4>
                                                <p class="card-text mID">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                                <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Number of shipments</h4>
                                                <p class="card-text nos">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                                <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Pieces</h4>
                                                <p class="card-text pieces">No Data</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    
                                    
                            </div>
                                <div class="row justify-content-center">
                                    <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Junctions</h4>
                                                <p class="card-text junctionC">No Data</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Bag created at</h4>
                                                <p class="card-text bag_created">No Data</p>
                                            </div>
                                        </div>
                                    </div></div>
                                    <div class="col-3"><div class="card text-center">
                                            <div class="card-content">
                                                <div class="card-body">
                                                    <h4 class="card-title success">Bag status updated at</h4>
                                                    <p class="card-text bsupated_at">No Data</p>
                                                </div>
                                            </div>
                                        </div></div>
                                    <div class="col-3"><div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title success">Status Hub</h4>
                                                <p class="card-text shub">No Data</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>

                            <div class="modal fade" id="junctions" role="dialog" aria-labelledby="junctions" aria-hidden="true">
                                <div class="modal-dialog modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        </div>
                                        <div class="modal-body text-center">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="bag_multiple_div" class="d-none">
                                <table class="table table-bordered datatable" id="datatable-2" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Bag Number</th>
                                        <th class="border-primary border-darken-1">Origin</th>
                                        <th class="border-primary border-darken-1">Destination</th>
                                        <th class="border-primary border-darken-1">Bag Status</th>
                                        <th class="border-primary border-darken-1">Bag Type</th>
                                        <th class="border-primary border-darken-1">Manifest ID</th>
                                        <th class="border-primary border-darken-1">Number Of Shipments</th>
                                        <th class="border-primary border-darken-1">Pieces</th>
                                        <th class="border-primary border-darken-1">Junction</th>
                                        <th class="border-primary border-darken-1">Bag Created At</th>
                                        <th class="border-primary border-darken-1">Bag Status Updated At</th>
                                        <th class="border-primary border-darken-1">Status Hub</th>
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

        #bag_single_div p.bstatus{
            font-weight: bold;
        }
        #bag_single_div{
            font-size: 20px;
        }
        #bag_single_div h4{
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

        .junctionC{
            padding: 10px;
            width: 100%;
        }

        #scan_bag {
            display: none;
        }

        #bag_multiple_div {
                display: none;
        }
        .btn_scn{
            margin-top: 7px;
        }

        .goldClass{	
            background-color: gold;	
        }	
        .yellowClass{	
            background-color: #86cd7c;	
        }	
        .greenClass{	
            background-color: springgreen;	
        }	
        .redClass{	
            background-color: red;	
            color:#fff;	
        }	
        .yellowClass{	
            background-color: yellow;	
        }	
        .cyanClass{	
            background-color: cyan;	
        }	
        .grey{	
            background-color: darkgrey;	
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
            var bag_table;
            var selection = true;
            var isSingleMode = true; 
            
            $('.single_multiple_switch').on('change',function(){
                var single_multiple_switch = document.querySelector('input.single_multiple_switch');
                isSingleMode = single_multiple_switch.checked;
                tracking_numbers_remarks = []
                $('#remarks_btn').addClass('d-none');

                if (isSingleMode === true) {
                   selection = true;
                   $('#multiple_div').addClass('d-none');
                   $('#bag_multiple_div').addClass('d-none');

                    destroyDatatable();
                } else if (isSingleMode === false) {

                    selection = false;
                    $('#multiple_div').removeClass('d-none');
                    $('#bag_multiple_div').removeClass('d-none');

                    $('#single_div').addClass('d-none');
                    $('#bag_single_div').addClass('d-none');
                    init();
                    initMultiple();

                }
            });

            function destroyDatatable() {
                if (typeof table !== 'undefined') {
                    table.clear();
                    table.destroy();
                    table = undefined;
                }
                if (typeof bag_table !== 'undefined') {
                    bag_table.clear();
                    bag_table.destroy();
                    bag_table = undefined;
                }
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
                    scrollX: true,
                    paging:false,
                    ordering:[0, 'desc'],
                    columns: [
                        {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                        {name: 'tracking_number', class: 'align-middle tracking_numbers', orderable: false},
                        {name: 'delivery_note_id', class: 'align-middle delivery_note_id', orderable: false},
                        {name: 'complaint', class: 'align-middle complaint', orderable: false},
                        {name: 'current_status', class: 'align-middle current_status', orderable: false},
                        {name: 'reason', class: 'align-middle reason', orderable: false},
                        {name: 'remarks', class: 'align-middle remarks', orderable: false},
                        {name: 'current_status_date', class: 'align-middle current_status_date', orderable: false},
                        {name: 'origin', class: 'align-middle origin', orderable: false},
                        {name: 'destination', class: 'align-middle destination', orderable: false},
                        {name: 'amount', class: 'align-middle amount', orderable: false},
                        {name: 'shipper', class: 'align-middle shipper', orderable: false},
                        {name: 'consignee_name', class: 'align-middle consignee_name', orderable: false},
                        {name: 'consignee_address', class: 'align-middle consignee_address', orderable: false}
                    ],
                    rowCallback: function(row, data, index) {
                        var complaint_id = $(row).find("td:eq(3)").html();
                        var status = parseInt($(row).attr('id'));
                        if(status === 13){
                            $(row).addClass('greenClass');
                        }else if(status === 12 || status === 52){
                            $(row).addClass('goldClass');
                        }else if(status === 20){
                            $(row).addClass('redClass');
                        }else if(status === 54){
                            $(row).addClass('yellowClass');
                        }else if(status === 55){
                            $(row).addClass('cyanClass');
                        }
                        else if(complaint_id !== '-'){
                            $(row).addClass('grey');
                        }
                    },
                    initComplete: function() {

                    }
                });
            }
            
                $(document).on('click', '#junction', function() {
                                    
                    $('#junctions .modal-body').html('');
                    var data = bag_table.row($(this).closest('tr')).data();

                    var head = '';
                    var junctions = '';
                    var date = '';

                    head = '<h4 class="modal-title" id="shipments_title">Junction(s)</h4>' +
                            '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                            '<span aria-hidden="true">×</span>\n' +
                            '</button>';

                               
                    if (data[9] !== undefined && data[9].length > 0) {
                        junctions = data[9].join(',');
                    } else {
                        junctions = (data[9] !== undefined) ? 'None' : '';
                    }

                            
                    $('#junctions .modal-header').html(head);
                    $('#junctions .modal-body').html(junctions);
                    $('#junctions').modal('show');
                        
                        
                });

            function initMultiple() {
                bag_table = $('#datatable-2').DataTable({
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    buttons:[{
                        extend: 'excel',
                        title: 'Quick Tracking (Bag)',
                        className: 'btn btn-primary mb-1',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
                    scrollX: true,
                    paging:false,
                    ordering:[0, 'desc'],
                    columns: [
                        {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                        {name: 'bag_number', class: 'align-middle bag_numbers', orderable: false},
                        {name: 'origin', class: 'align-middle origin', orderable: false},
                        {name: 'destination', class: 'align-middle destination', orderable: false},
                        {name: 'bag_status', class: 'align-middle bag_status', orderable: false},
                        {name: 'bag_type', class: 'align-middle bag_type', orderable: false},
                        {name: 'pieces', class: 'align-middle pieces', orderable: false},
                        {name: 'number_of_shipments', class: 'align-middle number_of_shipments', orderable: false},
                        {name: 'manifest_id', class: 'align-middle manifest_id', orderable: false},
                        {name: 'junction', class: 'align-middle junction', orderable: false},
                        {name: 'bag_created_at', class: 'align-middle bag_created_at', orderable: false},
                        {name: 'bag_status_updated_at', class: 'align-middle bag_status_updated_at', orderable: false},
                        {name: 'bag_status_hub', class: 'align-middle bag_status_hub', orderable: false}


                    ],
                    rowCallback: function(row, data, index) {
                        var info = bag_table.page.info();
                        $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                        if (data[9] !== undefined && data[9].length > 0) {
                            $('td:eq(9)', row).html("<button class='btn btn-sm btn-outline-info align-middle' id='junction'>" + data[9].length + "</button>");
                        } else {
                            $('td:eq(9)', row).html("0");
                        }

                        if (data[5] === 1) {
                            $('td:eq(5)', row).html("Normal");
                        } else {
                            $('td:eq(5)', row).html("Return");
                        }

                    },
                    initMultipleComplete: function() {

                    }
                });
            }

            

            $('#scan_tracking').on('change',function() {
                $(this).val($(this).val().trim());
            });

            $('#scan_tracking').keypress(function (e) {
                if (e.which == 13) {
                    $('form#quick_tracking_form').submit();
                }
            });

            $('#scan_bag').keypress(function (e) {
                if (e.which == 13) {
                    $('form#quick_tracking_form').submit();
                }
            });

            $('input#scan_tracking').focus();
            $('input#scan_bag').focus();


            $('#remarks_btn').on('click', function(){
                $('#AddRemarksModal').modal('show');
            })

            setTimeout(function () {
                $(".alert-success").fadeOut(1000);
            }, 3000); 
            
            var tracking_numbers_remarks = [];
            $('#quick_tracking_form').on('submit',function (e) {
                e.preventDefault();
                var scan = $('#scan_tracking');
                var tracking = scan.val();
                tracking_numbers_remarks.push(tracking);
                if (tracking_numbers_remarks.length > 0 ) {
                    $('#remarks_btn').removeClass('d-none');
                    $('#AddRemarksModal input[name="tracking_numbers"]').val(tracking_numbers_remarks);
                }
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
                                    scan_sound(2);
                                }else{
                                    var rowNo = table.rows().count();

                                    table.row.add([rowNo+1,parseInt(data.details.tracking_number),data.details.delivery_note_id,data.details.complaint,data.details.status,data.details.reason,data.details.remarks,data.details.current_status_date,data.details.origin,data.details.destination,data.details.amount,data.details.shipper,data.details.consignee_name,data.details.consignee_address]).node().id = data.details.status_id;
                                    table.draw(false);
                                    scan_sound(1);
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
                                        scan_sound(2);
                                    }else{
                                        var rowNo = table.rows().count();

                                        table.row.add([rowNo+1,parseInt(data.details.tracking_number),data.details.delivery_note_id,data.details.complaint,data.details.status,data.details.reason,data.details.remarks,data.details.current_status_date,data.details.origin,data.details.destination,data.details.amount,data.details.shipper,data.details.consignee_name,data.details.consignee_address]).node().id = data.details.status_id;
                                        table.draw(false);
                                        table.order([0, 'desc']).draw();
                                        scan_sound(1);
                                    }

                                    scan.val('');
                                    scan.attr('disabled', false);
                                    scan.focus();
                                });
                            }else{
                                var error = 'Tracking Number already scanned!';
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                scan_sound(2);
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
                                scan_sound(2);
                            }else{
                                $('#single_div').removeClass('d-none');
                                if($('#status_card').hasClass('greenClass') || $('#status_card').hasClass('redClass') || $('#status_card').hasClass('goldClass') || $('#status_card').hasClass('yellowClass') || $('#status_card').hasClass('cyanClass') || $('#status_card').hasClass('grey') ){
                                    $('#status_card').removeClass('greenClass');
                                    $('#status_card').removeClass('redClass');
                                    $('#status_card').removeClass('goldClass');
                                    $('#status_card').removeClass('yellowClass');
                                    $('#status_card').removeClass('cyanClass');
                                    $('#status_card').removeClass('grey');
                                }

                                scan_sound(1);
                                $('#single_div p.track').text(data.details.tracking_number);
                                $('#single_div p.status').text(data.details.status);
                                if(data.details.complaint == null){
                                    $('#single_div p.case_nature').text('No Complaint');
                                }else{
                                    $('#single_div p.case_nature').text(data.details.complaint);
                                }

                                $('#single_div p.origin').text(data.details.origin);
                                $('#single_div p.destination').text(data.details.destination);
                                $('#single_div p.amount').text(data.details.amount);
                                $('#single_div p.shipper').text(data.details.shipper);
                                $('#single_div p.consignee_name').text(data.details.consignee_name);
                                $('#single_div p.consignee_address').text(data.details.consignee_address);
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
                                    $('#status_card').addClass('goldClass');
                                }else if(data.details.status_id == 20){
                                    $('#status_card').addClass('redClass');
                                }else if(data.details.status_id == 54){
                                    $('#status_card').addClass('yellowClass');
                                }
                                else if(data.details.status_id == 55){
                                    $('#status_card').addClass('cyanClass');
                                }
                                else if(data.details.complaint != null){
                                    $('#status_card').addClass('grey');
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

            $('#quick_tracking_form').on('submit',function (e) {
                e.preventDefault();
                var scan = $('#scan_bag');
                var bag = scan.val();


                if (bag != '') {
                    scan.attr('disabled', true);
                    if(selection === false){
                        $('#bag_multiple_div').removeClass('d-none');
                        if(bag_table.row().count() == 0) {
                            $.ajax({
                                url:'{{route('admin.quick_tracking.info')}}',
                                type:'POST',
                                data: {
                                    'bag':bag,
                                    '_token': '{!! csrf_token() !!}'
                                }
                            }).done(function (data) {

                                if(data.status == 0){
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    scan_sound(2);
                                }else{
                                    var rowNo = bag_table.rows().count();

                                    bag_table.row.add([rowNo+1,parseInt(data.details.bag_number),data.details.origin,data.details.destination,data.details.bag_status,data.details.bag_type,data.details.manifest_id,data.details.number_of_shipments,data.details.pieces,data.details.junction,data.details.bag_created_at,data.details.bag_status_updated_at,data.details.bag_status_hub]).node().id = data.details.manifest_id;
                                    bag_table.draw(false);
                                    scan_sound(1);
                                }

                                scan.val('');
                                scan.attr('disabled', false);
                                scan.focus();
                            });
                        } else {
                            if(bag_table.columns('.bag_numbers').data().eq(0).indexOf(parseInt(bag)) === -1){
                                $.ajax({
                                    url:'{{route('admin.quick_tracking.info')}}',
                                    type:'POST',
                                    data: {
                                        'bag':bag,
                                        '_token':'{!! csrf_token() !!}'
                                    }
                                }).done(function (data) {
                                    if(data.status == 0){
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                        scan_sound(2);
                                    }else{
                                        var rowNo = bag_table.rows().count();

                                        bag_table.row.add([rowNo+1,parseInt(data.details.bag_number),data.details.origin,data.details.destination,data.details.bag_status,data.details.bag_type,data.details.manifest_id,data.details.number_of_shipments,data.details.pieces,data.details.junction,data.details.bag_created_at,data.details.bag_status_updated_at,data.details.bag_status_hub]).node().id = data.details.manifest_id;
                                        bag_table.draw(false);
                                        bag_table.order([0, 'desc']).draw();
                                        scan_sound(1);
                                    }

                                    scan.val('');
                                    scan.attr('disabled', false);
                                    scan.focus();
                                });
                            }else{
                                var error = 'Bag Number already scanned!';
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                scan_sound(2);
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
                                'bag':bag,
                                '_token': '{!! csrf_token() !!}'
                            }
                        }).done(function (data) {

                            if(data.status == 0){
                                $('#bag_single_div').addClass('d-none');
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                scan_sound(2);
                            }else{

                                
                                scan_sound(1);
                                var junctions = '';
                                $.each(data.details.junction, function(index, value) {
                                    junctions += value;
                                    if (index !== data.details.junction.length - 1) {
                                        junctions += ',';
                                    }
                                });
                                $('#bag_single_div').removeClass('d-none');

                                $('#bag_single_div p.bag').text(data.details.bag_number);
                                $('#bag_single_div p.origin').text(data.details.origin);
                                $('#bag_single_div p.destination').text(data.details.destination);
                                $('#bag_single_div p.bstatus').text(data.details.bag_status);
                                if (data.details.bag_type === 1)
                                {
                                    $('#bag_single_div p.btype').text('Normal');

                                }else{
                                    $('#bag_single_div p.btype').text('Return');

                                }
                                $('#bag_single_div p.mID').text(data.details.manifest_id);
                                $('#bag_single_div p.nos').text(data.details.number_of_shipments);
                                $('#bag_single_div p.pieces').text(data.details.pieces);

                                if (junctions.length > 0) {
                                    $('#bag_single_div p.junctionC').text(junctions);
                                } else {
                                    $('#bag_single_div p.junctionC').text('-');
                                }
                                $('#bag_single_div p.bag_created').text(data.details.bag_created_at);
                                $('#bag_single_div p.bsupated_at').text(data.details.bag_status_updated_at);
                                $('#bag_single_div p.shub').text(data.details.bag_status_hub);

                            }


                            scan.val('');
                            scan.attr('disabled', false);
                            scan.focus();
                        });

                    }
                }
            });

            $('#scan_btn').change(function() {
                console.log(tracking_numbers_remarks.length);
                var isChecked = $(this).is(':checked');
                if (!isChecked && tracking_numbers_remarks.length > 0 ) {
                    $('#remarks_btn').removeClass('d-none');

                }else{
                    $('#remarks_btn').addClass('d-none');

                }
            });

            $('#scan_btn').change(function() {
                var isChecked = $(this).is(':checked');
                if (isChecked) {
                    // If the checkbox is checked
                    $('#scan_bag').show();
                    $('#multiple_div').hide();
                    $('#bag_multiple_div').show();
                    $('#scan_tracking').hide();
                    $('#single_div').hide();
                    $('#bag_single_div').show();
                } else {
                    // If the checkbox is not checked
                    $('#scan_tracking').show();
                    $('#multiple_div').show();
                    $('#bag_multiple_div').hide();
                    $('#scan_bag').hide();
                    $('#bag_single_div').hide();
                    $('#single_div').show();
                }
            });
        });
    </script>
@endsection