@extends('admin.layout.master')

@section('title', 'Debriefing Report')
@section('content')
    <h1 class="mb-1">
        Debriefing Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">

                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_hub" id="search_hub" class="form-control select2">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_zone" id="search_zone" class="form-control select2">
                                @foreach($zones as $zone)
                                    <option value="{{$zone->id}}">{{$zone->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="form-group input-group col-4">
                        <div class="input-group-prepend">
										<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
											<span class="la la-calendar-o"></span>
										</span>
                        </div>

                        <input type="text" name="search_date" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date" placeholder="Search Date" data-value="{{Carbon\Carbon::now()}}">
                    </div>

                    <div class="col-2 text-center">
                        <button type="button" id="search_filter_btn" class="mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>

                    <div class="col-2 text-center">
                        <button type="button" id="export_btn" class="mb-1 btn btn-outline-primary btn-min-width"><i class="la la-file-excel-o"></i> Export</button>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">Hubs</th>
                            <th class="border-red border-darken-1 bg-white">Status Not Updated</th>
                            <th class="border-primary border-darken-1">Delivered</th>
                            <th class="border-primary border-darken-1">Delivery Unsuccessful</th>
                            <th class="border-primary border-darken-1">On Hold</th>
                            <th class="border-primary border-darken-1">Confirmation Pending</th>
                            <th class="border-primary border-darken-1">Lost</th>
                            <th class="border-primary border-darken-1">Confirm</th>
                            <th class="border-primary border-darken-1">Correct Status</th>
                            <th class="border-red border-darken-1 bg-white">Fake Status</th>
                            <th class="border-primary border-darken-1">Total</th>
                            <th class="border-primary border-darken-1">Ratio</th>
                            <th class="border-primary border-darken-1">Delivery Note Pending</th>
                            <th class="border-primary border-darken-1">Total</th>
                            <th class="border-primary border-darken-1">Ratio</th>
                            <th class="border-primary border-darken-1">Delivery Tomorrow</th>
                            <th class="border-primary border-darken-1">Grand Total</th>
                            <th class="border-primary border-darken-1">Ratio</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Hub',
                width:'100%',
                allowClear:true
            });

            $('#search_zone').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Zone',
                width:'100%',
                allowClear:true
            });

            var date = '{{ Carbon\Carbon::now()}}';

            $('#search_date').pickadate({
                firstDay: 1,
                clear: '',
                max:date,
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#search_date_root').css('top','40px');
                }
            });

            var table = $('#datatable').DataTable({
                dom: 'tp',
                scrollX: true,
                paging: false,
                ordering: false,
                columnDefs: [
                    {className: 'red', targets: [1, 9]}
                ]
            });

            var types = ['status_not_updated', 'delivered', 'delivery_unsucessful', 'on_hold', 'confirmation_pending', 'lost', 'confirm', 'correct_status', 'fake_status', 'total_1', 'total_1_ratio', 'delivery_note_pending', 'total_2', 'total_2_ratio', 'delivery_tomorrow', 'grand_total', 'grand_total_ratio'];

            function list() {
                blockPagePermanently();
                $('#search_filter_btn').prop('disabled', true);
                $('#export_btn').prop('disabled', true);

                $.ajax({
                    url: '{!! route('admin.reports.debriefing.list') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'search_date' : search_date,
                        'search_hub' : search_hub,
                        'search_zone' : search_zone
                    }
                })
                .done(function(data) {
                    if (data.status == 0) {
                        table.clear();

                        $.each(data.counts, function(hub, count) {
                            var row = [];

                            row.push(hub);

                            $.each(types, function(index, type) {
                                row.push(count[type]);
                            });

                            table.row.add(row);
                        });

                        table.draw();
                    }
                    else {
                        table.clear().draw();
                    }

                    $('#search_filter_btn').prop('disabled', false);
                    $('#export_btn').prop('disabled', false);
                    UnblockPagePermanently();
                });
            }

            var search_date = $('input[name="search_date_formatted"]').val();
            var search_hub = $('#search_hub').val();
            var search_zone = $('#search_zone').val();

            $('#search_filter_btn').on('click', function() {
                search_date = $('input[name="search_date_formatted"]').val();
                search_hub = $('#search_hub').val();
                search_zone = $('#search_zone').val();

                list();
            });

            $('#export_btn').on('click', function() {
                search_date = $('input[name="search_date_formatted"]').val();
                search_hub = $('#search_hub').val();
                search_zone = $('#search_zone').val();

                window.open('{!! route('admin.reports.debriefing.export') !!}?search_date=' + search_date + '&search_hub=' + search_hub + '&search_zone=' + search_zone, '_blank');
            });
        });

    </script>
@endsection