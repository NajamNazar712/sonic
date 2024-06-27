@extends('admin.layout.master')
@section('title','Service Charges Ledger')


@section('content')
    <h1 class="mb-1">
        Service Charges Ledger
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form action="{{ route('admin.finance.service_charges_ledger.list') }}" method="GET" id="search_form">
                    <div class="row">
                        <div class="col-4">
                            <label for="">Shipper Name</label>
                            <select name="shippers" id="shippers" class="form-control select2">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
    
                        <div class="col-4">
                            <label for="">Date (From)</label>
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                </div>
                                <input type="text" name="from_date" id="from_date" class="form-control pickadate bg-primary border-primary white rounded-right" placeholder="Date (From)">
                            </div>
                        </div>
    
                        <div class="col-4">
                            <label for="">Date (To)</label>
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                </div>
                                <input type="text" name="to_date" id="to_date" class="form-control pickadate bg-primary border-primary white rounded-right" placeholder="Date (To)">
                            </div>
                        </div>
                    </div>

                    <div class="" id="search_btn_div">
                        <button type="button" class="btn btn-primary" id="search_btn">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>
    $(document).ready(function() {
        // from date
        $('#search_form #from_date').pickadate({
            firstDay: 1,
            clear: '',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 00:00:00',
            hiddenSuffix: '_formatted',
            onSet: function(context) {
                if (context.select) {
                    $('#search_form #from_date').pickadate('picker').set('min', $('#search_from #from_date').pickadate('picker').get('select'));
                }
            }
        });

        // to date
        $('#search_form #to_date').pickadate({
            firstDay: 1,
            clear: '',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 00:00:00',
            hiddenSuffix: '_formatted',
            onSet: function(context) {
                if (context.select) {
                    $('#search_form #to_date').pickadate('picker').set('min', $('#search_from #to_date').pickadate('picker').get('select'));
                }
            }
        });

        $('#shippers').prepend('<option selected></option>').select2({
            placeholder: 'Select a Shipper',
            width: '100%',
            allowClear: true
        });

        $('#search_btn').on('click', function () {
            var formData = {
                shippers: $('#shippers').val(),
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val()
            };
            $.ajax({
                url: "{{ route('admin.finance.service_charges_ledger.list') }}",
                data: {formData},
                success: function (response) {
                    console.log(response);
                }
            });
        });
    });
    </script>

@endsection