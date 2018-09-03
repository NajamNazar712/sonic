@extends('admin.layout.master')

@section('content')
    <h1 class="mb-1">
        Monthwise Customer Sales Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <div class="col-12">
                    <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate" method="post" action="{{route('admin.reports.customer_sales.export_to_excel')}}">
                        <div class="col-2">
                        <div class="form-group">
                            <select name="city" class="select2" id="city">
                                @foreach($hubs as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        </div>
                        <div class="col-2">
                        <div class="form-group ml-1">
                            <select name="shipper" class="select2" id="shipper">
                                @foreach($shippers as $shipper)
                                    <option value="{{ $shipper->id }}">{{ $shipper->name }}</option>
                                @endforeach
                            </select>
                        </div></div>
                        <div class="col-3">
                        <div class="form-group input-group ml-1">

                                <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                                </span>
                                </div>
                                <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-rule-required="true" data-msg-required="Date(From) is required">

                        </div></div>
                        <div class="col-3">
                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                                <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-rule-required="true" data-msg-required="Date(To) is required">

                        </div></div>

                        <div class="col-2">
                        <div class="form-group ml-1">
                            <button type="submit" name="search" class="btn btn-primary" value="Search">Search</button>
                        </div>
                        </div>
                    </form>
                </div>
                </div>
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
            border-color: #666EE8;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #666ee8;
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
        #from_date_table{
            display:none;
        }
        #to_date_table{
            display:none;
        }
    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_form #city').prepend('<option value="" selected="selected"></option>').select2({
                width: '200px',
                placeholder: 'Select Hub',
                allowClear:true
            });
            $('#search_form #shipper').prepend('<option value="" selected="selected"></option>').select2({
                width: '200px',
                placeholder: 'Select Shipper',
                allowClear:true
            });

            var max = '{{ Carbon\Carbon::now() }}';

            var from_date = $('#from_date').pickadate({
                    firstDay: 1,
                    disable:[true],
                    clear: '',
                    today:'Select Current Month',
                    max: max,
                    format:'mmmm, yyyy',
                    selectYears: true,
                    selectMonths: true,
                    formatSubmit: 'yyyy-mm-dd 00:00:00',
                    hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                },
                onSet: function(context) {

                    var from_month = $('#from_date_root .picker__select--month').val();
                    var from_year = $('#from_date_root .picker__select--year').val();
                    var from_date_selected = new Date(from_year,from_month, 1);

                    from_date.pickadate('picker').set('select', from_date_selected,{muted:true});
                    var current_date_formatted = $('input[name="from_date_formatted"]').val();
                    to_date.pickadate('picker').set('min',new Date(current_date_formatted),{muted:true});
                    $('#to_date_root button.picker__button--today').removeAttr('disabled');
                    $('#from_date_root button.picker__button--today').removeAttr('disabled');
                }
                // onClear: function() {
                    // from_date.pickadate('picker').set('clear');
                    // $('input[name="from_date_formatted"]').val('');
                    // var from_month = $('.picker__select--month').val('');
                    // var from_year = $('.picker__select--year').val('');
                    // from_date.pickadate('picker').clear();
                // },

            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                disable:[true,1],
                clear: '',
                today:'Select Current Month',
                max: max,
                format:'mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    $('#to_date_root button.picker__button--today').removeAttr('disabled');
                    $('#from_date_root button.picker__button--today').removeAttr('disabled');
                    var to_month = $('#to_date_root .picker__select--month').val();
                    var to_year = $('#to_date_root .picker__select--year').val();
                    var to_date_selected = new Date(to_year,to_month, 1);

                    to_date.pickadate('picker').set('select',to_date_selected,{muted:true});

                    var current_date_formatted = $('input[name="to_date_formatted"]').val();


                    from_date.pickadate('picker').set('max',new Date(current_date_formatted),{muted:true});
                    // console.log(new Date(current_date_formatted,1))
                }
            });
            $('#to_date_root button.picker__button--today').removeAttr('disabled');
            $('#from_date_root button.picker__button--today').removeAttr('disabled');
            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {


                    var from_date = $('#search_form input[name="from_date_formatted"]').val();
                    var to_date = $('#search_form input[name="to_date_formatted"]').val();
                    var city = $('#city').val();
                    var shipper = $('#shipper').val();
                    $.ajax({
                        url: '{!! route('admin.reports.customer_sales.export_to_excel') !!}',
                        method: 'post',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'from_date': from_date,
                            'to_date': to_date,
                            'city': city,
                            'shipper': shipper,
                        }
                    }).done(function (data) {
                        // window.open("",'_black');
                        if(data.success == 1){
                            window.open("{!! route('admin.reports.customer_sales.download') !!}",'_black');
                        }else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                        }
                    });
                    return false;
                }
            });

        });

    </script>
@endsection