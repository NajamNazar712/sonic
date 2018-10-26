@extends('admin.layout.master')

@section('title', 'Booked VS Received')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Booked VS Received
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="search_form" class="form" nonvalidate="nonvalidate" >

                            <div class="row mb-2 justify-content-center">
                                <div class="col">
                                    <div class="form-group">

                                            <select name="search_city" id="search_city" class="form-control select2" data-rule-required="true" data-msg-required="City is required">
                                                @foreach($cities as $city)
                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group input-group ">
                                        <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                        </div>

                                        <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)" data-rule-required="true" data-msg-required="Date is required">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group input-group ">
                                        <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                        </div>

                                        <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" data-rule-required="true" data-msg-required="Date is required">
                                    </div>
                                </div>
                                    <div class="col">
                                        <button type="submit" id="search_filter_btn" class=" btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                    </div>


                            </div>
                            </form>
                                <div id="booked_table_div" style="min-height: 300px;"></div>
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

    <script>
        $(document).ready(function() {
            $('#search_city').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search City',
                width:'100%',
                allowClear:true
            });
            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            $( "#search_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {

                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    var city_select = $('#search_city').val();
                    var search_from = $('input[name="search_date_from_formatted"]').val();
                    var search_to = $('input[name="search_date_to_formatted"]').val();

                    $.ajax({
                        url: '{!! route('admin.pickups.bookedvsreceived.list') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'city_select': city_select,
                            'search_from': search_from,
                            'search_to': search_to,

                        }
                    }).done(function (data) {

                        if(data.status === 1){
                            var shipment = '';
                            shipment += '<table class="table table-bordered datatable " id="datatable" style="z-index: 3;">' +
                                '                    <thead>' +
                                '                    <tr class="bg-primary white">' +
                                '                        <th class="border-primary border-darken-1">Shipper</th>' +
                                '                        <th class="border-primary border-darken-1">Booked</th>' +
                                '                        <th class="border-primary border-darken-1">Received</th>' +
                                '                    </tr>' +
                                '                    </thead>';
                            shipment += '<tbody>';
                            $.each(data.shipments,function (id,details) {
                                shipment += '<tr><td class="align-middle shipper">'+details.shipper+'</td>';
                                shipment += '<td class="align-middle booked"><button class="btn btn-sm btn-outline-info align-middle">'+details.booked+'</button></td>';
                                shipment += '<td class="align-middle received">'+details.received+'</td></tr>';
                            });
                            shipment += '</tbody></table>';
                            $('#booked_table_div').html('');
                            $('#booked_table_div').html(shipment);
                            var table = $('#datatable').DataTable({
                                scrollX: true, scrollY: '350px',
                                dom: '<"d-inline-block"><"pull-right"B>t',
                                buttons: [
                                    {
                                        extend: 'excel',
                                        title: 'Booked VS Received',
                                        text:'<i class="la la-file-excel-o"></i> Excel',
                                    },
                                ],
                                paging:false,
                                ordering: false,
                                columns: [
                                    {name: 'shipper', class: 'align-middle shipper'},
                                    {name: 'booked', class: 'align-middle booked'},
                                    {name: 'received', class: 'align-middle received'}

                                ],
                                initComplete: function() {
                                    this.api().table().columns.adjust();
                                }
                            });
                            $(form).find('button[type=submit]').attr('disabled', false);

                        }
                    });

                }


            });
            $('#datatable tbody').on('click', 'tr td.booked', function() {

            });

        });
    </script>
@endsection