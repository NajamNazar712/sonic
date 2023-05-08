@extends('admin.layout.master')

@section('title', 'Rider Pickup Action Logs')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <h1 class="mb-1">
                Rider Pickup Action Logs
            </h1>

            <div class="card">
                <div class="card-content" aria-expanded="true">
                    <div class="card-body">
                        @include('admin.inc.messages')

                        <!-- <div class="row justify-content-center">
                                <div class="col-3">
                                    <label class="font-medium-2 font-weight-bold block">Old Rider Pickup Action Logs</label>
                                    <div class="form-group">
                                        <label for="old_rider_pickup_action_log" class="font-medium-2 text-bold-600 mr-1">No</label>
                                        <input type="checkbox" name="old_rider_pickup_action_log" id="old_rider_pickup_action_log" class=" old_rider_pickup_action_log" data-size="sm" data-switchery="true">
                                        <label for="old_rider_pickup_action_log" class="font-medium-2 text-bold-600 ml-1">Yes</label>
                                    </div>
                                </div>
                            </div> -->

                        <div id="search_form" class="row mb-2 justify-content-center">
                            <div class="col-4">
                                <fieldset class="form-group">
                                    <input type="text" class="form-control" name="search_pn_no" id="search_pn_no" placeholder="Enter Pickup Number">
                                </fieldset>
                            </div>
                            <div class="col-4">
                                <fieldset class="form-group">
                                    <select name="search_assigned_by" id="search_assigned_by" class="form-control select2">
                                        @foreach($admins as $admin)
                                        <option value="{{$admin->id}}">{{$admin->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-4">
                                <fieldset class="form-group">
                                    <select name="search_rider" id="search_rider" class="form-control select2">
                                        @foreach($riders as $rider)
                                        <option value="{{$rider->id}}">{{$rider->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-4">
                                <fieldset class="form-group">
                                    <select name="search_city" id="search_city" class="form-control select2">
                                        @foreach($cities as $city)
                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-4">
                                <fieldset class="form-group">
                                    <select name="search_city_area" id="search_city_area" class="form-control select2">

                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-4 ">

                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o"></span>
                                        </span>
                                    </div>

                                    <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Assigned Date (From)">
                                </div>
                            </div>
                            <div class="col-4 ">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o"></span>
                                        </span>
                                    </div>
                                    <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Assigned Date (To)">
                                </div>

                            </div>
                            <div class="col-2">
                                <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                            </div>
                        </div>

                        <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                            <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">Logged At</th>
                                    <th class="border-primary border-darken-1">Rider</th>
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Address</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Area</th>
                                    <th class="border-primary border-darken-1">Action Type</th>
                                    <th class="border-primary border-darken-1">Pickup Note ID</th>
                                    <th class="border-primary border-darken-1">Pickup Request ID</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
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
@endsection

@section('js')
<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

<script>
    $(document).ready(function() {

        $('#search_pn_no').inputmask({
            'alias': 'integer'
            , 'allowMinus': false
            , 'allowPlus': false
        });

        $('#search_assigned_by').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Search Assigned By'
            , width: '100%'
            , allowClear: true
        });
        $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Search Rider'
            , width: '100%'
            , allowClear: true
        });
        $('#search_city').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Search City'
            , width: '100%'
            , allowClear: true
        }).bind('change', function() {
            var cityId = $(this).val();

            $('#search_city_area').empty();

            if (cityId === '') {
                $('#search_city_area').prop('disabled', true);
                return;
            }

            $('#search_city_area').prop('disabled', false);

            $.ajax({
                url: '{{ route('admin.v2_pickups.action_log.getCityAreas') }}'
                , type: 'GET'
                , data: {
                    city_id: cityId
                }
                , dataType: 'json'
                , success: function(response) {
                    $.each(response, function(index, area) {
                        $('#search_city_area').append('<option value="' + area.id + '">' + area.name + '</option>');
                    });
                }
                , error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });

        $('#search_city_area').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Search City Area'
            , width: '100%'
            , allowClear: true
        });

        $('#search_form #search_date_from').pickadate({
            firstDay: 1
            , clear: ''
            , selectYears: true
            , selectMonths: true
            , formatSubmit: 'yyyy-mm-dd 00:00:00'
            , hiddenSuffix: '_formatted'
            , onSet: function(context) {
                if (context.select) {
                    $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                }
            }
        });
        $('#search_form #search_date_to').pickadate({
            firstDay: 1
            , clear: ''
            , selectYears: true
            , selectMonths: true
            , formatSubmit: 'yyyy-mm-dd 23:59:59'
            , hiddenSuffix: '_formatted'
            , onSet: function(context) {
                if (context.select) {
                    $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                }
            }
        });

        jQuery.fn.DataTable.Api.register('buttons.exportData()', function(options) {
            if (this.context.length) {
                body = [];
                var params = table.ajax.params();
                params.start = 0;
                params.length = -1;
                params.excel = true;
                var jsonResult = $.ajax({
                    url: '{{ route('admin.v2_pickups.action_log.list') }}'
                    , data: params
                    , success: function(result) {
                        head = [];

                        head.push('Logged At');
                        head.push('Rider');
                        head.push('Shipper');
                        head.push('Address');
                        head.push('City');
                        head.push('Area');
                        head.push('Action Type');
                        head.push('Pickup Note ID');
                        head.push('Pickup Request ID');


                        $.each(result.data, function(index, values) {
                            row = [];

                            row.push(values.logged_at);
                            row.push(values.rider);
                            row.push(values.shipper);
                            row.push(values.pickup_address);
                            row.push(values.city);
                            row.push(values.city_area_name);
                            row.push(values.type);
                            row.push(values.pickup_note_id);
                            row.push(values.pickup_request_id);

                            body.push(row);
                        });
                    }
                    , async: false
                });

                return {
                    body: body
                    , header: head
                };
            }
        });


        var table = $('#datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr'
            , buttons: [{
                extend: 'excel'
                , title: 'Rider Pickup Action Logs'
                , className: 'btn btn-primary'
                , text: '<i class="la la-file-excel-o"></i> Excel'
            }, 'reset']
            , scrollX: true
            , lengthMenu: [
                [10, 50, 100, 500, 1000, -1]
                , [10, 50, 100, 500, 1000, 'All']
            ]
            , pageLength: 10
            , pagingType: 'full_numbers'
            , processing: true
            , language: {
                processing: data_table_loader
            }
            , serverSide: true
            , ajax: {
                url: '{{ route('admin.v2_pickups.action_log.list') }}'
                , data: function(d) {
                    d.search_pn_no = $('#search_pn_no').val();
                    d.search_rider = $('#search_rider').val();
                    d.search_assigned_by = $('#search_assigned_by').val();
                    d.search_city = $('#search_city').val();
                    d.search_city_area = $('#search_city_area').val();
                    d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                    d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                }
            }
            , order: [
                [0, 'desc']
            ]
            , columns: [{
                    data: 'logged_at'
                    , name: 'v2_rider_pickup_action_logs.logged_at'
                    , class: 'align-middle logged_at'
                }
                , {
                    data: 'rider'
                    , name: 'r.name'
                    , class: 'align-middle rider'
                }
                , {
                    data: 'shipper'
                    , name: 'u.name'
                    , class: 'align-middle shipper'
                }
                , {
                    data: 'pickup_address'
                    , name: 'usi.pickup_address'
                    , class: 'align-middle pickup_address'
                }
                , {
                    data: 'city'
                    , name: 'c.name'
                    , class: 'align-middle city'
                }
                , {
                    data: 'city_area_name'
                    , name: 'cas.name'
                    , class: 'align-middle city_area_name'
                }
                , {
                    data: 'type'
                    , name: 'v2_rider_pickup_action_logs.type_id'
                    , class: 'align-middle type'
                }
                , {
                    data: 'pickup_note_id'
                    , name: 'pn.id'
                    , class: 'align-middle pickup_note_id'
                }
                , {
                    data: 'pickup_request_id'
                    , name: 'v2_rider_pickup_action_logs.pickup_request_id'
                    , class: 'align-middle pickup_request_id'
                }
            ]
            , initComplete: function() {
                var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                var type_select = '<select name="type_select" id="type_select" class="select2 form-control"></select>';

                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();

                    if ($(header).is('.picture_path')) {
                        $(td).appendTo($(search));
                    } else if ($(header).is('.type')) {
                        $(type_select).appendTo($(search)).on('change', function() {
                            column.search($(this).val(), false, false, true).draw();
                        }).wrap(td);
                    } else {
                        var current = $(input).appendTo($(search)).on('change', function() {
                            column.search($(this).val(), false, false, true).draw();
                        }).wrap(td).after(icon);

                        if (column.search()) {
                            current.val(column.search());
                        }
                    }
                });

                var pickup_actions = $.map({!!$pickup_actions!!}, function(obj) {
                    obj.id = obj.id;
                    obj.text = obj.name;

                    return obj;
                });

                $('#type_select').prepend('<option value="" selected></option>').select2({
                    data: pickup_actions
                    , placeholder: "Select Type"
                    , width: '100%'
                    , containerCssClass: 'select-xs'
                    , dropdownCssClass: 'form-control-sm p-0'
                });

                this.api().table().columns.adjust();
            }
        });
        $('#search_filter_btn').on('click', function() {
            table.draw();
        });
    });

</script>
@endsection
