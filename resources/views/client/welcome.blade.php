@extends('client.layout.master')

@section('title', 'Welcome')

@section('content')

    <div class="card">

        @include('client.inc.messages')
        <div class="card-content" aria-expanded="true">
            <div class="card-body text-center">
                <div class="row mb-5">
                    <div class="col d-flex justify-content-center" style="margin: 0px 0px 0px 277px;">
                        <h1>Welcome to Sonic..</h1>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('cod.packaging.requests.categories') }}" class="btn btn-primary" data-toggle="tooltip" title="Click to order your packaging materials now" data-placement="bottom">
                            <i class="la la-bookmark"></i>
                            Packaging Material Request
                        </a>
                    </div>
                </div>
        
                @if(isset($user->lead_id))
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%; background-color: {{ $color }};" aria-valuenow="{{ $percentage }}">
                            {{ $percentage }}%
                        </div>
                    </div>

                    <div style="border: 1px solid #ccc; padding: 20px; border-radius: 10px; max-width: 600px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
                        <div style="text-align: left; flex-grow: 1;">
                            @if ($short_description != "")
                               <h2>Your Account Status: {{$short_description}}</h2> 
                            @endif
                            <p style="margin-top:20px;">{{ $description }}</p>
                            @if(($user->on_board_status < 1 && session('request_custom_quotation') != 1))
                                <a href="{{ route('cod.wordpress.register') }}" style="display: inline-block; padding: 10px 20px; color: white; background-color: #007bff; border-radius: 5px; text-decoration: none;">Start Onboarding</a>
                            @endif
                        </div>
                        <div style="flex-shrink: 0; margin-left: 20px;">
                            <img src="{{ asset('img/proposed_lead_image_onboard.png') }}" alt="Onboarding Image" style="width: 150px; height: auto;">
                        </div>
                    </div>


                    

                @endif
            
                <div class="row mt-2">
                        <div class="col">
                            <table class="table table-bordered">
                                @if(count($sales_person_data)> 0)
                                    <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1"><b>Sales Person Name</b></th>
                                        <th class="border-primary border-darken-1"><b>Sales Person Phone</b></th>
                                        <th class="border-primary border-darken-1"><b>Sales Person Email</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td><h4>{{$sales_person_data['name']}}</h4></td>
                                        <td><h4>{{$sales_person_data['phone']}}</h4></td>
                                        <td><h4>{{$sales_person_data['email']}}</h4></td>
                                    </tr>
                                    </tbody>
                                @endif
                                @if(count($poc)> 0)
                                    <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1"><b>POC Name</b></th>
                                        <th class="border-primary border-darken-1"><b>POC Phone</b></th>
                                        <th class="border-primary border-darken-1"><b>POC Email</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($poc as $p)
                                        <tr>
                                            <td><h4>{{$p['name']}}</h4></td>
                                            <td><h4>{{$p['phone']}}</h4></td>
                                            <td><h4>{{$p['email']}}</h4></td>
                                        </tr>
                                        @break
                                    @endforeach
                                    </tbody>
                                @endif
                                @if(count($kam)> 0)
                                    <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1"><b>KAM Name</b></th>
                                        <th class="border-primary border-darken-1"><b>KAM Phone</b></th>
                                        <th class="border-primary border-darken-1"><b>KAM Email</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($kam as $k)
                                        <tr>
                                            <td><h4>{{$k['name']}}</h4></td>
                                            <td><h4>{{$k['phone']}}</h4></td>
                                            <td><h4>{{$k['email']}}</h4></td>
                                        </tr>
                                        @break
                                    @endforeach
                                    </tbody>
                                @endif
                            </table>
                            @if(count($pickup_riders)> 0)
                                <h2>Pickup Courier Details</h2>
                                <table class="table table-bordered">

                                    <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1"><b>Courier Name</b></th>
                                        <th class="border-primary border-darken-1"><b>Courier Phone</b></th>
                                        <th class="border-primary border-darken-1"><b>City</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($pickup_riders as $rider)
                                        <tr>
                                            <td><h4>{{$rider->name}}</h4></td>
                                            <td><h4>{{$rider->phone}}</h4></td>
                                            <td><h4>{{$rider->city}}</h4></td>
                                        </tr>
                                        @break
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                            <div class="row mt-2 mb-1">
                                <div class="col-auto mr-1">
                                    <a href="javascript:void(0)" id="loadSarReport" class="btn btn-primary">
                                        <i class="la la-bar-chart"></i>
                                        Click here to view Summary of SAR
                                    </a>
                                </div>
                                <div class="col d-flex justify-content-center" >
                                    <h2>Summary of Shipper Advice Request</h2>
                                </div>
                                <div class="col-auto ml-1">
                                    <a href="{{ route('cod.return.pending.index') }}" class="btn btn-primary"  data-placement="bottom">
                                        <i class="la la-rotate-left"></i>
                                        Shipper Advise Requested
                                    </a>
                                </div>
                            </div>
                            <table class="table table-bordered" id="sarReportTable" style="width: 100%">
                                <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">SAR Date</th>
                                        <th class="border-primary border-darken-1">Shipments</th>
                                        <th class="border-primary border-darken-1">Return-Confirm Date</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    {{--@if($shipper_payments != null)
                        <div class="col-4">
                            <h3 class="mb-2">Payments</h3>
                            <div class="card">
                                <div id="funnel-plot" class="height-400 echart-container"></div>
                            </div>
                        </div>
                    @endif--}}
                    </div>
                </div>

        </div>
    </div>

    @if (session('user_type') == 1 && session()->has('phone_number_unverified'))
        <div class="modal fade" id="PasswordModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="PasswordModal"
             aria-hidden="true" style="top:30%;">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white text-center">
                        <h4 class="modal-title white">OTP Verification</h4>

                    </div>
                    <div class="modal-body  text-center">

                        <div class="row justify-content-center">
                            <div class="form-group form-inline">
                                <input type="text" class="form-control password" autofocus id="password_input" placeholder="Enter Verification Code">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button tabindex="-1" type="button" class="btn btn-primary ml-1" id="password_submit" disabled>Enter</button>
                        <button type="button" class="btn btn-info ml-1" id="password_close">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">

    <style>
        .gauge{
            height: 85px;
            overflow: hidden;
            position: relative;
            width: 170px;
        }
        .gauge .arc{
            background-image:
                    radial-gradient(#fff 0, #fff 60%, transparent 60%),
                    conic-gradient(red 0, orange 135deg, #ccc 135deg, #ccc 180deg, #fff 180deg, #fff 360deg);
            background-position: center center, center center;
            background-repeat: no-repeat;
            background-size: 100% 100%, 100% 100%;
            border-radius: 50%;
            border-style: none;
            height: 170px;
            position: relative;
            transform: rotate(-90deg);
            width: 100%;
        }
        .gauge .pointer{
            background: #fff;
            border: 1px solid #000;
            border-radius: 5px;
            bottom: 0;
            content: '';
            height: 6px;
            left: 0;
            position: absolute;
            transform: rotate(135deg) translateX(2px) translateY(-6px);
            transform-origin: 85px 0;
            width: 20px;
            z-index: 5;
        }
        .gauge .mask::before,
        .gauge .mask::after{
            background-image: radial-gradient(transparent 0, transparent 50%, #fff 50%, #fff 100%);
            clip-path: polygon(0 50%, 100% 50%, 100% 100%, 0% 100%);
            content: '';
            height: 18px;
            position: absolute;
            width: 18px;
        }
        .gauge .mask::before{
            left: -2px;
            bottom: 0;
        }
        .gauge .mask::after{
            bottom: 0;
            right: -2px;
        }
        .gauge .label{
            bottom: 20px;
            font-size: 16px;
            font-weight: 700;
            left: 0;
            line-height: 26px;
            position: absolute;
            text-align: center;
            width: 100%;
        }

        .tooltip .tooltip-inner {
            text-align: left;
        }
        .tooltip {
            margin-left: -100px;
        }

    </style>
@endsection
@section('js')

    <script src="{{asset('app-assets/vendors/js/charts/chartjs/chart.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function(){

            var sarTable = null;

            $('#loadSarReport').on('click', function () {

                // already loaded then reload
                if (sarTable !== null) {
                    sarTable.ajax.reload();
                    return;
                }

                sarTable = $('#sarReportTable').DataTable({
                    dom: 't<"bottom"ip>',
                    scrollX: true,
                    scrollY: '500px',
                    paging: false,
                    info: true,
                    searching: false,
                    ordering: false,
                    lengthChange: false,
                    processing: true,
                    language: {
                        processing: data_table_loader
                    },
                    serverSide: true,
                    ajax: {
                        url: '{{ route("cod.sar_report") }}',
                    },
                    order: [[0, 'desc']],
                    columns: [
                        { data: 'sar_date', name: 'sar_date', class: 'align-middle text-center' },
                        { data: 'total_shipments', name: 'total_shipments', class: 'align-middle text-center' },
                        { data: 'return_confirm_date', name: 'return_confirm_date', class: 'align-middle text-center' },
                    ],
                    initComplete: function() {
                        this.api().table().columns.adjust();
                    }
                });
            });


        @if ($user->lead_id)
                @if (session('status') != 3 && $user->status == 3)
                    var warning = 'Dear Shipper, We are pleased to inform you that your account has been successfully activated at 100%. Please log in again to use the portal. Thank you for choosing our services';
                    toastr.warning(warning, 'Note!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center',
                        timeOut: 30000 
                    });

                @endif


                @if (session('status') == 0 && $user->agreement_signed == 0 && $user->request_custom_quotation == 1)
                        var url = '{!! route('cod.updateSignOffCrf') !!}';
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}", 
                                user_id: "{{ $user->id }}" 
                            },
                            success: function(response) {
                                console.log('Success:', response);
                            },
                            error: function(xhr) {
                                console.log('Error:', xhr.responseText);
                            }
                        });
                @endif
                
            @endif

            
            @if (session('user_type') == 1 && session()->has('phone_number_unverified'))

            $('#password_input').inputmask({
                'mask': '99999',
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'clearIncomplete': true
            });
                $('#PasswordModal').modal('show');

            $('body').on('keypress change','#password_input',function() {
                $('#password_submit').attr('disabled', true);
                if($(this).val().length == 5){
                    $('#password_submit').attr('disabled', false);
                }
            });
            $('#password_submit').on('click', function () {
                var pass = $('#password_input').val();

                if(pass){
                    $.ajax({
                        url: '{!! route('cod.opt_verify') !!}',
                        type: 'POST',
                        data: {
                            'code': pass,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status == 0){
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            $('#password_input').val('');
                            $('#password_submit').attr('disabled', true);
                        }else{
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            $('#PasswordModal').modal('hide');
                            $('#password').val(pass);
                        }
                    });
                }
                else{
                    var error = 'Please Enter OTP Code!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });
            $('#password_close').on('click', function(){

                $.ajax({
                    url: '{!! route('cod.opt_verify_close') !!}',
                }).done(function (data) {
                    if(data.status){
                        $('#password_input').val('');
                        $('#PasswordModal').modal('hide');
                    }
                });

            });
            @endif

            {{--@if($shipper_payments != null)
                var myChart = echarts.init(document.getElementById('funnel-plot'));

                // Chart Options
                // ------------------------------
                chartOptions = {

                    // Add tooltip
                    tooltip : {
                        trigger: 'item',
                        formatter: "{b} : {c}"
                    },

                    // Add legend
                    legend: {
                        data : ['Pending','Processed','Paid']
                    },

                    // Add Custom Colors
                    color: ['#00A5A8','#FF7D4D','#28D094'],

                    // Add series
                    series : [
                        {
                            name:'Funnel plot',
                            type:'funnel',
                            itemStyle: {
                                normal: {
                                    label: {
                                        formatter: "{c}"
                                    },
                                    labelLine: {
                                        show : false
                                    }
                                }
                            },
                            // width: '40%',
                            data:[
                                {value:"{{$shipper_payments->total_pending}}", name:'Pending', title:"{{number_format($shipper_payments->total_pending)}}"},
                                {value:"{{$shipper_payments->total_process}}", name:'Processed'},
                                {value:"{{$shipper_payments->total_paid}}", name:'Paid'}
                            ]
                        }
                    ]
                };

                // Apply options
                myChart.setOption(chartOptions);

                // Resize chart
                $(function () {

                    // Resize chart on menu width change and window resize
                    $(window).on('resize', resize);
                    $(".menu-toggle").on('click', resize);

                    // Resize function
                    function resize() {
                        setTimeout(function() {

                            // Resize chart
                            myChart.resize();
                        }, 200);
                    }
                });
            });

            
            @endif--}}

        });
    </script>

@endsection