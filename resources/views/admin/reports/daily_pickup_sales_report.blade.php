@extends('admin.layout.master')

@section('title', 'Daily Pickup & Sales Report')

@section('content')
    <h1 class="mb-1">
        Daily Pickup & Sales Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                        <div class="form-group">
                            <select name="city" class="select2" id="city">
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(in_array(session('id'), session('sale_users_bypass')) )
                        <div class="form-group ml-1">
                            <select name="sales_person" class="select2" id="sales_person">
                                @foreach($sales_persons as $person)
                                    <option value="{{ $person->id }}">{{ $person->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
										<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
											<span class="la la-calendar-o"></span>
										</span>
                            </div>

                            <input type="text" name="search_date" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date" placeholder="Search Date" data-value="{{Carbon\Carbon::now()}}">
                        </div>

                        <div class="form-group ml-1">
                            <button type="submit" name="search" class="btn btn-primary" value="Search">Search</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_form #city').prepend('<option value="" selected="selected"></option>').select2({
                width: '200px',
                placeholder: 'Select City',
                allowClear:true
            });
            $('#search_form #sales_person').prepend('<option value="" selected="selected"></option>').select2({
                width: '200px',
                placeholder: 'Select Sales Person',
                allowClear:true
            });
            // $('#search_form #shipment_type').prepend('<option value="" selected="selected"></option>').select2({
            //     width: '200px',
            //     placeholder: 'Select Shipment Type'
            // });
            var date = '{{ Carbon\Carbon::now()}}';
            $('#search_form #search_date').pickadate({
                firstDay: 1,
                clear: '',
                max:date,
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#search_date_root').css('top','40px');
                },
                onSet: function(context) {

                }
            });
            $('#search_form').on('submit',function (e) {
                e.preventDefault();
                // blockPagePermanently();
                var search_date = $('#search_form input[name="search_date_formatted"]').val();
                var city = $('#city').val();
                var shipping_modes = $('#shipping_mode').val();
                var sales = '';
                @if(in_array(session('id'), session('sale_users_bypass')))
                sales = $('#sales_person').val();
                @endif
                $.ajax({
                    url: '{!! route('admin.reports.daily_pickup_sales.export_to_excel') !!}',
                    method: 'post',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'date': search_date,
                        'city': city,
                        'sales_person': sales,
                        'sales_tagging':1
                    }
                }).done(function (data) {
                    if(data.status === 1) {

                        var route = '{!! url('/') !!}' + '/' + data.file_name;

                        window.open(route, '_black');

                        {{--$.ajax({--}}
                            {{--url: '{!! route('admin.reports.daily_pickup_sales.download') !!}',--}}
                            {{--method: 'post',--}}
                            {{--data: {--}}
                                {{--'_token': '{{ csrf_token() }}',--}}
                                {{--'file': data.file_name,--}}
                            {{--}--}}
                        {{--}).done(function (data) {--}}

                        {{--});--}}
                    }
                    // UnblockPagePermanently();
            });
        });
    });


    </script>
@endsection