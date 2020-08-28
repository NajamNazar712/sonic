@extends('admin.layout.master')

@section('title', 'Receiving Sheet')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Receiving Sheet
                </h1>

                <div class="card height-400">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-center mb-2" id="search_form">
                                <div class="col-3">
                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                        </div>
                                        <input type="text" name="pickup_date" class="form-control bg-primary border-primary white rounded-right" id="pickup_date" placeholder="Date" data-rule-required="true" data-msg-required="Date is required">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <fieldset class="form-group">
                                        <select name="search_rider" id="search_rider" class="form-control select2">
                                            @foreach($riders as $rider)
                                                <option value="{{$rider->id}}">{{$rider->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>


                                <div class="col-2">
                                    <button type="button" id="search_filter_btn"  class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function () {
            var pickup_date = $('#pickup_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                max: '{!! Carbon\Carbon::now() !!}',
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#pickup_date_root').css('top','40px');
                },
            });



            function print(id) {
                $.ajax({
                    url: '{!! route('admin.v2_pickups.receiving_sheet.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }
            $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Rider*',
                width: '100%',
                allowClear: true
            });


            $('#search_filter_btn').on('click', function () {
                var errors = 0;
                var rider = $('#search_rider').val();
                var date = $('input[name="pickup_date_formatted"]').val();
                if(rider == null || rider == ''){
                    var error = "Rider not selected!";
                    errors = 1;
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(date == null || date == ''){
                    var error = "Date not selected!";
                    errors = 1;
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(errors == 0){
                    $.ajax({
                        url: '{!! route('admin.v2_pickups.receiving_sheet.check_pickup') !!}',
                        method: 'POST',
                        data: {
                            'rider_id': rider,
                            'pickup_date': date,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status == 0){
                            print(data.pickup_note_id);
                        }else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                        }
                    });
                }


            });
        });
    </script>
@endsection