@extends('client.layout.master')

@section('title', 'Welcome')

@section('content')

    <div class="card">
        @include('client.inc.messages')
        <div class="card-content" aria-expanded="true">
            <div class="card-body text-center">
                <h1 class="mb-5">Welcome to Sonic..</h1>
                <div class="row">
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
                                @endforeach
                                </tbody>
                            </table>
                        @endif
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
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function(){
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
    </script>

@endsection