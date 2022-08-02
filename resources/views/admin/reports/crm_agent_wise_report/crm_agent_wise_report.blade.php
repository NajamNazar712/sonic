@extends('admin.layout.master')

@section('title', 'Agent Wise Report')

@section('content')
    <h1 class="mb-1">
        Agent Wise Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')


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
                                <select name="agent_id" id="agent_id" class="form-control agent_id select2">
                                    @foreach($agents as $agent)
                                        <option value="{{$agent->id}}">{{$agent->name}}</option>
                                    @endforeach
                                </select>
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
        .border_none{
            border-top: none !important;
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

            $("#agent_id").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Agent",
                width:'100%',
                containerCssClass: 'select-md',
                dropdownCssClass: 'form-control-sm p-0'
            });

            var from_max = '{{ Carbon\Carbon::yesterday()}}';

            $('#date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                }
            });
            var flag = true;
            $('#search_filter_btn').on('click',function () {
                flag = true;
                blockPagePermanently();

                var search_date_from = $('input[name="date_from_formatted"]').val();
                var agent_id = $('#agent_id').val();
                if(search_date_from == null || search_date_from == ''){
                    flag = false;
                }
                if(flag){
                    $.ajax({
                        url: '{!! route('admin.reports.crm_agent_wise_report.list') !!}',
                        method: 'POST',
                        data: {
                            'search_date_from': search_date_from,
                            'agent_id': agent_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        console.log(data);
                        var shipment = '';
                        shipment += '<table class="table table-bordered datatable " id="datatable" style="z-index: 3; width:100%;">' +
                            '                    <thead>' +
                            '                    <tr class="bg-primary white">' +

                            '                        <th class="border-primary border-darken-1">S.No</th>' +
                            '                        <th class="border-primary border-darken-1">Agent</th>' +
                            '                        <th class="border-primary border-darken-1">Pending</th>' +
                            '                        <th class="border-primary border-darken-1">New Assign</th>' +
                            '                        <th class="border-primary border-darken-1">Total</th>' +
                            '                        <th class="border-primary border-darken-1">Closure</th>' +
                            '                        <th class="border-primary border-darken-1">Remaining</th>' +
                            '' +
                            '                    </tr></thead>';

                            shipment += '<tbody>';
                            var i = 0;
                            var counter = 0;
                        $.each(data,function (index, details) {
                            console.log(details);
                            counter++;
                            shipment += '<tr><td class="align-middle serial_number">'+counter+'</td>';
                            shipment += '<td class="align-middle serial_number">'+details.name+'</td>';
                            shipment += '<td class="align-middle pending">'+details.pending+'</td>';
                            shipment += '<td class="align-middle new_launched">'+details.new_assign+'</td>';
                            shipment += '<td class="align-middle total">'+details.total+'</td>';
                            shipment += '<td class="align-middle closed">'+details.closed+'</td>';
                            shipment += '<td class="align-middle remaining">'+(details.total - details.closed)+'</td>';
                            shipment +='</tr>';
                            
                            
                            // $.each(details,function (index_crm_count, filter_data) {
                            //     console.log(filter_data.name);
                                
                            // });

                        });
                        shipment += '</tbody>';
                       
                        shipment += '</table>';

                        $('#crm_count_table').html(shipment);

                        var table = $('#datatable').DataTable({
                            scrollX: true, scrollY: '500px',
                            dom: '<"d-inline-block"><"pull-right"B>t',
                            buttons: [
                                    @if (session('role_id') == 1 || in_array(784, session('permissions')))
                                {
                                    extend: 'excelHtml5',
                                    footer: true,
                                    title: 'Agent Wise Complaint Report',
                                    text:'<i class="la la-file-excel-o"></i> Excel',
                                    action: function (e, dt, node, config) {
                                        var that = this;
                                        $.ajax({
                                            url: '{!! route('admin.reports.crm_agent_wise_report.list') !!}',
                                            method: 'POST',
                                            data: {
                                                '_token': '{{ csrf_token() }}',
                                                'excel': true,
                                            }
                                        }).done(function (data) {
                                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(that,e, dt, node, config);
                                        });
                                    },

                                },
                                    @endif
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
                            ],
                            initComplete: function() {
                                this.api().table().columns.adjust();
                            }
                        });

                    });
                    UnblockPagePermanently();
                }else{
                    UnblockPagePermanently();
                    var error = "Select Date From!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }



            });


        });

    </script>
@endsection