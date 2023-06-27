@extends('admin.layout.master')

@section('title', 'Rider Receiving')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Rider Receiving
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="track_form" class=" mb-1 justify-content-center" novalidate="novalidate">
                                <div class="row justify-content-center mb-2" id="search_form">
                                    <div class="col-3">
                                        <fieldset class="form-group">
                                            <select name="search_rider" id="search_rider" class="form-control select2">
                                                @foreach($riders as $rider)
                                                    <option value="{{$rider->id}}">{{$rider->name}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-3">
                                        <fieldset class="form-group">
                                            <select name="search_city" id="search_city" class="form-control select2">
                                                @foreach($cities as $city)
                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-3">
                                        <fieldset class="form-group">
                                            <select name="search_area" id="search_area" class="form-control select2">
                                                @foreach($areas as $area)
                                                    <option value="{{$area->id}}">{{$area->name}} - {{ $area->hubs->name }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-3">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                </span>
                                            </div>
                                            <input type="text" name="search_date_from"  data-value="{{$default_date}}"  class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_from" placeholder="Receiving Sheet From">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o small-calender-icon"></span>
                                            </span>
                                            </div>
                                            <input type="text" name="search_date_to"  data-value="{{$default_date}}" class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_to" placeholder="Receiving Sheet To">
                                        </div>
                                    </div>

                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-2">
                                        <button type="button" id="search_filter_btn"  class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1 ">Pickup Note#</th>
                            <th class="border-primary border-darken-1 ">Pickup Note Date</th>
                            <th class="border-primary border-darken-1">Rider Name</th>
                            <th class="border-primary border-darken-1">Total Shipment</th>
                            <th class="border-primary border-darken-1">Rider Picked</th>
                            <th class="border-primary border-darken-1">Arrived at Origin</th>
                        </thead>
                    </table>
                </div>

            </div>

        </div>
    </div>
    <div class="modal fade" id="total_shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="total_shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="total_shipments_modal_title">Total Shipment(s)</h4>

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
    <div class="modal fade" id="total_shipments_arrived_modal" data-backdrop="static" role="dialog" aria-labelledby="total_shipments_arrived_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="total_shipments_arrived_modal_title">Arrived Shipment(s)</h4>

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
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function () {
            $('#search_area').attr("disabled", true);
            $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Rider',
                width: '100%',
                allowClear: true
            });
            $('#search_city').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select City',
                width: '100%',
                allowClear: true
            }).bind("change",function(){
                var area_list = $('#search_area');
                let id = $(this).val();
                area_list.attr("disabled", true);
                if(id) {
                    $.ajax({
                        url: '{!! route('admin.human_resource.employee_directory.get_area') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'city_id': id
                        }
                    })
                    .done(function (data) {
                        area_list.empty();
                        if(data.areas.length > 0){
                            area_list.attr("disabled", false);
                            $.each(data.areas, function (key, value) {
                                var newOption = "<option value="+ value.id +">" + value.name + "</option>";
                                area_list.append(newOption);
                            });
                        }else{
                            area_list.attr("disabled", true);
                        }

                    });
                }
            });
            $("#search_area").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Area",
                allowClear: true,
                width: '100%',
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.v2_pickups.rider_receiving.list') }}',
                        data: params,
                        success: function (result)
                        {
                            head = [];
                            head.push('S.No');
                            head.push('Pickup Note#');
                            head.push('Pickup Note Date');
                            head.push('Rider Name');
                            head.push('Total Shipment');
                            head.push('Arrived at Origin');
                            head.push('Rider Picked');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.id);
                                row.push(values.date);
                                row.push(values.rider);
                                row.push(values.total_shipment_count);
                                row.push(values.total_arrived_count);
                                row.push(values.rider_picked);
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );

            var search_date_from = $('#track_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 06:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    var old_date_formatted = $('input[name="search_date_from_formatted"]').val();
                    var contractMoment = moment(old_date_formatted);
                    var current = moment(contractMoment).add(3, 'days');
                    search_date_to.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                    search_date_to.pickadate('picker').set('max', new Date(current.toDate()),{muted:true});
                    search_date_to.pickadate('picker').set('select', new Date(old_date_formatted),{muted:true});
                }
            });

            var search_date_to = $('#track_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 06:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    // if (context.select) {
                    //     $('#track_form #search_date_from').pickadate('picker').set('max', $('#track_form #search_date_to').pickadate('picker').get('select'));
                    // }
                }
            });

            var table = $('#datatable').DataTable({
                scrollX: false, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Rider Receiving Sheet',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o "></i> Excel',
                    },'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.v2_pickups.rider_receiving.list') }}',
                    data: function (d) {
                        d.search_rider = $('select[name="search_rider"]').val();
                        d.search_city = $('select[name="search_city"]').val();
                        d.search_area = $('select[name="search_area"]').val();

                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();

                    }
                },
                rowId: 'id',
                order: [[1, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'note_id', name: 'v2_pickup_notes.id', class: 'align-middle text_center note_id'},
                    {data: 'date', name: 'v2_pickup_notes.created_at', class: 'align-middle text_center date'},
                    {data: 'rider', name: 'r.name', class: 'align-middle text_center rider'},
                    {data: 'total_shipment', name: 'total_shipment_count', class: 'text_center text-center total_shipment'},
                    {data: 'rider_picked', name: 'rider_picked', class: 'align-middle rider_picked'},
                    {data: 'total_arrived', name: 'total_arrived', class: 'text_center text-center total_arrived'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    // var type_select = '<select name="type_select" id="type_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.total_shipment') || $(header).is('.total_arrived') || $(header).is('.rider_picked')) {
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    this.api().table().columns.adjust();
                }

            });
            $('#search_filter_btn').bind('click', function (e) {
                table.draw();
            });

              function print(id) {
                   $.ajax({
                       url: '{!! route('admin.v2_pickups.rider_receiving.print') !!}',
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

            $('.datatable tbody').on('click', 'tr td.note_id button.print', function() {
                print(parseInt($(this).children('.id').html()));
            });
            $('#datatable tbody').on('click','tr td.note_id .btn-group', function() {

                var rider = table.row($(this).parents('tr')).data().rider;
                var date = table.row($(this).parents('tr')).data().date;
                $.ajax({
                    url: '{!! route('admin.v2_pickups.rider_receiving.check_pickup') !!}',
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


            });

            /* $('#search_filter_btn').on('click', function () {
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
                         url: '{!! route('admin.v2_pickups.rider_receiving.check_pickup') !!}',
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


            });*/
            var route = '{!! route('admin.tracking.index') !!}';
            $('body').on('click','#datatable tbody tr td.total_shipment button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#total_shipments_modal .modal-body').html('');
                $('#total_shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.v2_pickups.rider_receiving.total_shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'note_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var shipments = '';
                            if (data.booked) {
                                $.each(data.booked, function(index, tracking_numbers) {
                                    shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';
                                });
                            }
                            $('#total_shipments_modal .modal-body').html(shipments);
                        }
                    });
            });

            $('body').on('click','#datatable tbody tr td.total_arrived button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                var total_received = parseInt($(this).parents('tr').attr('id'));
                $('#total_shipments_arrived_modal .modal-body').html('');
                $('#total_shipments_arrived_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.v2_pickups.rider_receiving.arrived_shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'note_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var shipments = '';
                            if (data.arrived) {
                                $.each(data.arrived, function(index, tracking_numbers) {
                                    shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';
                                });
                            }
                            $('#total_shipments_arrived_modal .modal-body').html(shipments);
                        }
                    });
            });
        });
    </script>
@endsection
