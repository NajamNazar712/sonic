@extends('admin.layout.master')

@section('title', 'CRM Counts')

@section('content')
    <h1 class="mb-1">
        CRM Counts
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')


                    {{--<div class="col-3">--}}
                        {{--<fieldset class="form-group">--}}
                            {{--<input type="text" name="search_date" class="form-control bg-primary border-primary white rounded-right" id="search_date" placeholder="Search Date" data-value="">--}}
                        {{--</fieldset>--}}
                    {{--</div>--}}

                    <div class="row justify-content-center">
                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                </div>

                                <input type="text" name="date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="date_from" placeholder="Date (From)">
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                </div>

                                <input type="text" name="date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="date_to" placeholder="Date (To)">
                            </div>
                        </div>
                        <div class="col">
                            <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                        </div>

                    </div>




                <div id="crm_count_table"></div>
               
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style>
        .nodisplay{
            display: none;
        }
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
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #64a0d2;
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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            var from_max = '{{ Carbon\Carbon::yesterday()}}';

            $('#date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#date_to').pickadate('picker').set('min', $('#date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#date_from').pickadate('picker').set('max', $('#date_to').pickadate('picker').get('select'));
                    }
                }
            });
            var flag = true;
            $('#search_filter_btn').on('click',function () {
                flag = true;
                blockPagePermanently();
                // var table = '';

                var search_date_from = $('input[name="date_from_formatted"]').val();
                var search_date_to = $('input[name="date_to_formatted"]').val();
                if(search_date_from == null){
                    flag = false;
                    var error = "Select Date From!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(search_date_from == null || search_date_to == null){
                    flag = false;
                    var error = "Select Date To!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(flag){
                    $.ajax({
                        url: '{!! route('admin.reports.crm_count.list') !!}',
                        method: 'POST',
                        data: {
                            'search_date_from': search_date_from,
                            'search_date_to': search_date_to,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        var shipment = '';
                        // var cargo_pending = 0;
                        // var cargo_resolved = 0;
                        // var cargo_transit_pending = 0;
                        // var cargo_transit_resolved = 0;
                        // var deliveries_pending = 0;
                        // var deliveries_resolved = 0;
                        // var receive_deliveries_pending = 0;
                        // var receive_deliveries_resolved = 0;
                        // var return_marked_pending = 0;
                        // var return_marked_resolved = 0;
                        // var return_confirmed_pending = 0;
                        // var return_confirmed_resolved = 0;
                        // var return_cargo_pending = 0;
                        // var return_cargo_resolved = 0;
                        // var return_delivery_pending = 0;
                        // var return_delivery_resolved = 0;
                        // var return_receive_pending = 0;
                        // var return_receive_resolved = 0;
                        // var total_pendings = 0;
                        // var grand_total_pendings = 0;
                        // var total_resolved = 0;
                        // var grand_total_resolved = 0;
                        // var total_unresolved = 0;
                        // var grand_total_unresolved = 0;
                        shipment += '<table class="table table-bordered datatable " id="datatable" style="z-index: 3;">' +
                            '                    <thead>' +
                            '                    <tr class="bg-primary white">' +

                            '                        <th class="border-primary border-darken-1">S.No</th>' +
                            '                        <th class="border-primary border-darken-1">Date</th>' +
                            '                        <th class="border-primary border-darken-1">Pending</th>' +
                            '                        <th class="border-primary border-darken-1">New Launch</th>' +
                            '                        <th class="border-primary border-darken-1">Total</th>' +
                            '                        <th class="border-primary border-darken-1">Closure</th>' +
                            '                        <th class="border-primary border-darken-1">Remaining</th>' +
                            '                        <th class="border-primary border-darken-1">% Of Closure</th>' +
                            '                        <th class="border-primary border-darken-1">% Of Remaining</th>' +
                            '                        <th class="border-primary border-darken-1">Weekly Remaining Avg</th>' +
                            '                        <th class="border-primary border-darken-1">Weekly Closure Avg</th>' +
                            '' +
                            '                    </tr></thead>';

                            shipment += '<tbody>';
                            var i = 0;
                            var counter = 0;
                            console.log(data);
                            console.log(data.count_days);
                            // $.each(data,function (index ,details) {
                            //     console.log("details");
                            //     console.log(details);
                            //     console.log(details.data);
                            //     console.log('index');
                            //     console.log(index);
                            // });
                        $.each(data,function (index, details) {
                            
                            // total_pendings = details.cargo_pending + details.cargo_transit_pending + details.deliveries_pending + details.receive_deliveries_pending + details.return_marked_pending + details.return_confirmed_pending + details.return_cargo_pending + details.return_delivery_pending + details.return_receive_pending;
                            // total_resolved = details.cargo_resolved + details.cargo_transit_resolved + details.deliveries_resolved + details.receive_deliveries_resolved + details.return_marked_resolved + details.return_confirmed_resolved + details.return_cargo_resolved + details.return_delivery_resolved + details.return_receive_resolved;
                            // total_unresolved = total_pendings - total_resolved;
                            // grand_total_pendings += total_pendings;
                            // grand_total_resolved += total_resolved;
                            // grand_total_unresolved += total_unresolved;
                            i = 0;
                            $.each(details.data,function (index_crm_count, crm_counts) {
                                console.log('crm_counts');
                                console.log(crm_counts);
                                console.log('crm_counts.date');
                                console.log(crm_counts.date);
                                if(i == 0){
                                    counter++;

                                    shipment += '<tr><td class="align-middle serial_number">'+counter+'</td>';
                                    shipment += '<td class="align-middle serial_number">'+crm_counts.date+'</td>';
                                    shipment += '<td class="align-middle pending">'+crm_counts.pending+'</td>';
                                    shipment += '<td class="align-middle new_launched">'+crm_counts.new_launched+'</td>';
                                    shipment += '<td class="align-middle total">'+crm_counts.total+'</td>';
                                    shipment += '<td class="align-middle closed">'+crm_counts.closed+'</td>';
                                    shipment += '<td class="align-middle remaining">'+crm_counts.remaining+'</td>';
                                    shipment += '<td class="align-middle closure_percent">'+crm_counts.closure_percent+'</td>';
                                    shipment += '<td class="align-middle remaining_percent">'+crm_counts.remaining_percent+'</td>';
                                    shipment += '<td class="align-middle weekly_remaining" rowspan="'+details.count_days+'">'+details.weekly_remaining+'</td>';
                                    shipment += '<td class="align-middle weekly_close" rowspan="'+details.count_days+'">'+details.weekly_close+'</td>';
                                    shipment +='</tr>';
                                }else{
                                    shipment += '<tr><td class="align-middle stations">'+counter+'</td>';
                                    shipment += '<td class="align-middle date">'+crm_counts.date+'</td>';
                                    shipment += '<td class="align-middle pending">'+crm_counts.pending+'</td>';
                                    shipment += '<td class="align-middle new_launched">'+crm_counts.new_launched+'</td>';
                                    shipment += '<td class="align-middle total">'+crm_counts.total+'</td>';
                                    shipment += '<td class="align-middle closed">'+crm_counts.closed+'</td>';
                                    shipment += '<td class="align-middle remaining">'+crm_counts.remaining+'</td>';
                                    shipment += '<td class="align-middle closure_percent">'+crm_counts.closure_percent+'</td>';
                                    shipment += '<td class="align-middle remaining_percent">'+crm_counts.remaining_percent+'</td>';
                                    shipment +='</tr>';

                                }
                                i++;
                            });

                        });
                        shipment += '</tbody>';
                       
                        shipment += '</table>';
                        console.log(shipment);
                        $('#crm_count_table').html(shipment);

                        var table = $('#datatable').DataTable({
                            scrollX: true, scrollY: '500px',
                            dom: '<"d-inline-block"><"pull-right"B>t',
                            buttons: [
                                {
                                    extend: 'excelHtml5',
                                    footer: true,
                                    title: 'QA Report',
                                    text:'<i class="la la-file-excel-o"></i> Excel',
                                    action: function (e, dt, node, config) {
                                        var that = this;
                                        $.ajax({
                                            url: '{!! route('admin.reports.crm_count.list') !!}',
                                            method: 'POST',
                                            data: {
                                                '_token': '{{ csrf_token() }}',
                                                'excel': true,
                                            }
                                        }).done(function (data) {
                                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(that,e, dt, node, config);
                                        });
                                    },

                                    customize: function (xlsx) {
                                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                        var numrows = 1;
                                        var rows = $('row', sheet);

                                      //update Row


                                        // var new_sheet = rows.slice(1);
                                        $.each(rows,function () {
                                            var attr = $(this).attr('r');
                                                var ind = parseInt(attr);
                                                ind = ind + numrows;
                                                $(this).attr("r", ind);
                                        });

                                    //     // Create row before data

                                        $('row c', sheet).each(function () {
                                                var attr = $(this).attr('r');
                                                var pre = attr.substring(0, 1);
                                                var ind = parseInt(attr.substring(1, attr.length));
                                                ind = ind + numrows;
                                                $(this).attr("r", pre + ind);

                                        });


                                        var merge_cells = '';
                                        first_row = '<row r="1"><c r="A1" t="inlineStr" s="51"><is><t>QA Report</t></is></c></row>';
                                        function Addrow(index,data) {
                                            msg='<row r="'+index+'">';
                                            for(i=0;i<data.length;i++){
                                                var key=data[i].key;
                                                var range=data[i].range;
                                                var value=data[i].value;
                                                msg += '<c t="inlineStr" s="2" r="' + key + index + '">';
                                                msg += '<is>';
                                                msg +=  '<t>'+value+'</t>';
                                                msg+=  '</is>';
                                                msg+='</c>';

                                                merge_cells += '<mergeCell ref="' + key + index + ':' + range + index + '"/>';
                                            }
                                            msg += '</row>';

                                            return msg;
                                        }

                                    //     //insert
                                        var second_row = Addrow(2, [{ key: 'A',range:'A', value: '' }, { key: 'B',range:'C', value: 'Parcel Pending for Cargo' },{ key: 'D',range:'E', value: 'Cargo In Transit' },{ key: 'F',range:'G', value: 'Pending Deliveries' },{ key: 'H',range:'I', value: 'Receive Delivery Note' },{ key: 'J',range:'K', value: 'Return Marked' },{ key: 'L',range:'M', value: 'Confirmed Returns' },{ key: 'N',range:'O', value: 'Return Cargo In Transit' },{ key: 'P',range:'Q', value: 'Return Pending for Delivery' },{ key: 'R',range:'S', value: 'Receive Return Note' },{ key: 'T',range:'V', value: 'Grand Total' }]);

                                        sheet.childNodes[0].childNodes[1].innerHTML = first_row + second_row + sheet.childNodes[0].childNodes[1].innerHTML;
                                        sheet.childNodes[0].childNodes[2].innerHTML =  sheet.childNodes[0].childNodes[2].innerHTML + merge_cells;

                                        // console.log(sheet.childNodes[0].childNodes[1].innerHTML);

                                        // console.log(sheet.childNodes[0].childNodes[2].innerHTML);
                                    }
                                },
                            ],
                            paging:false,
                            ordering: false,
                            columns: [
                                {name: 'serial_number', class: 'align-middle serial_number'},
                                {name: 'date', class: 'align-middle date'},
                                {name: 'pending', class: 'align-middle pending'},
                                {name: 'new_launched', class: 'align-middle new_launched'},
                                {name: 'total', class: 'align-middle total'},
                                {name: 'closed', class: 'align-middle closed'},
                                {name: 'remaining', class: 'align-middle remaining'},
                                {name: 'closure_percent', class: 'align-middle closure_percent'},
                                {name: 'remaining_percent', class: 'align-middle remaining_percent'},
                                {name: 'weekly_remaining', class: 'align-middle grand_total_resolved'},
                                {name: 'weekly_close', class: 'align-middle grand_total_unresolved'}
                            ],
                            initComplete: function() {
                                this.api().table().columns.adjust();
                            }
                        });

                    });
                    UnblockPagePermanently();
                }else{
                    UnblockPagePermanently();
                    var error = "Select all dates!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }



            });


        });

    </script>
@endsection