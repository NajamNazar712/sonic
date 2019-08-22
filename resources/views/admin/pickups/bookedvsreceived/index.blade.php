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
                            <input type="hidden" name="city_id" id="city_id">
                            <input type="hidden" name="date_from" id="date_from">
                            <input type="hidden" name="date_to" id="date_to">
                                <div id="booked_table_div" style="min-height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    <div class="modal fade" id="booked_shipments" role="dialog" aria-labelledby="booked_shipments_title" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="booked_shipments_title">Booked Shipment(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="received_shipments" role="dialog" aria-labelledby="received_shipments_title" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="received_shipments_title">Received Shipment(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

                    $('#city_id').val(city_select);
                    $('#date_from').val(search_from);
                    $('#date_to').val(search_to);
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
                            var total_booked = 0;
                            var total_received = 0;
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
                                shipment += '<tr id="'+ details.shipper_id+'"><td class="align-middle shipper">'+details.shipper+'</td>';
                                shipment += '<td class="align-middle text-center booked"><button class="btn btn-sm btn-outline-info">'+details.booked+'</button></td>';
                                shipment += '<td class="align-middle text-center received"><button class="btn btn-sm btn-outline-info">'+details.received+'</button></td></tr>';
                                total_booked = total_booked + details.booked;
                                total_received = total_received + details.received;
                            });
                            shipment += '<tr id="total"><td class="align-middle shipper">Total</td>';
                            shipment += '<td class="align-middle text-center booked">'+total_booked+'</td>';
                            shipment += '<td class="align-middle text-center received">'+total_received+'</td></tr>';
                            shipment += '</tbody></table>';
                            $('#booked_table_div').html('');
                            $('#booked_table_div').html(shipment);
                            var table = $('#datatable').DataTable({
                                scrollX: true, scrollY: '500px',
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
            $('body').on('click', 'tr td.booked button', function() {
                var shipper_id = parseInt($(this).parents('tr').attr('id'));
                var city = $('#city_id').val();
                var date_from = $('#date_from').val();
                var date_to = $('#date_to').val();

                $.ajax({
                    url: '{!! route('admin.pickups.bookedvsreceived.booked') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'shipper_id':shipper_id,
                        'city_id': city,
                        'search_from': date_from,
                        'search_to': date_to,
                    }
                }).done(function (data) {
                    if (data.status == 1) {
                        var tracking_numbers = '';

                        $.each(data.shipments, function(index, tracking_number) {
                            tracking_numbers += tracking_number.tracking_number + '<br/>';
                        });

                        $('#booked_shipments .modal-body').html(tracking_numbers);

                        $('#booked_shipments').modal('show');
                    }
                });

            });
            $('body').on('click', 'tr td.received button', function() {
                var shipper_id = parseInt($(this).parents('tr').attr('id'));
                var city = $('#city_id').val();
                var date_from = $('#date_from').val();
                var date_to = $('#date_to').val();

                $.ajax({
                    url: '{!! route('admin.pickups.bookedvsreceived.received') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'shipper_id':shipper_id,
                        'city_id': city,
                        'search_from': date_from,
                        'search_to': date_to,
                    }
                }).done(function (data) {
                    if (data.status == 1) {
                        var tracking_numbers = '';

                        $.each(data.shipments, function(index, tracking_number) {
                            tracking_numbers += tracking_number.tracking_number + '<br/>';
                        });

                        $('#received_shipments .modal-body').html(tracking_numbers);

                        $('#received_shipments').modal('show');
                    }
                });

            });

        });
    </script>
@endsection