@extends('admin.layout.master')

@section('title', 'Pickups History')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Pickups History
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div id="search_form" class="row mb-2 justify-content-center">
                                <div class="col-4">
                                    <div class="form-group input-group ml">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                        </div>
                                        <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Search Date (From)">
                                    </div>
                                </div>
                                <div class="col-4 ">
                                    <div class="form-group input-group ml">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                        </div>
                                        <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Search Date (To)">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Pickup Note No.</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">No. Of Pickups</th>
                                    <th class="border-primary border-darken-1">No. Of Shipments</th>
                                    <th class="border-primary border-darken-1">Rider</th>
                                    <th class="border-primary border-darken-1">Assigned By</th>
                                    <th class="border-primary border-darken-1">Assigned Date</th>
                                    <th class="border-primary border-darken-1">Completed By</th>
                                    <th class="border-primary border-darken-1">Completed Date</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!--Shipments popup -->
    <div class="modal fade" id="bookings_modal" data-backdrop="static" role="dialog" aria-labelledby="bookings_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="bookings_modal_title">Booking Shipment(s)</h4>

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
    <!--Shipments popup -->
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
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
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
            function print(id) {
                $.ajax({
                    url: '{!! route('admin.pickups.assigned.print') !!}',
                    method: 'POST',
                    data: {
                        'ids': [id],
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
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.pickups.history.list') }}',
                        data: {
                            'page': 'all',
                            'search_date_from': $('input[name="search_date_from_formatted"]').val(),
                            'search_date_to': $('input[name="search_date_to_formatted"]').val()
                        },
                        success: function (result) {
                            head = [];
                            head.push('S. No');
                            head.push('Pickup Note No.');
                            head.push('Status');
                            head.push('City');
                            head.push('No. Of Pickups');
                            head.push('No. Of Shipments');
                            head.push('Rider');
                            head.push('Assigned By');
                            head.push('Assigned Date');
                            head.push('Completed By');
                            head.push('Completed Date');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.pn_id);
                                row.push(values.status);
                                row.push(values.city);
                                row.push(values.pickups);
                                row.push(values.bookings);
                                row.push(values.rider);
                                row.push(values.assigned_by);
                                row.push(values.assigned_date);
                                row.push(values.completed_by);
                                row.push(values.completed_date);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '350px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Pickups History',
                        className:'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.pickups.history.list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'pn_id',
                order: [[8, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'pickup_notes.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'pickup_note_no', name: 'pickup_notes.id', class: 'align-middle pickup_note_no'},
                    {data: 'status', name: 'pickup_notes.status_id', class: 'align-middle status'},
                    {data: 'city', name: 'cities.name', class: 'align-middle city'},
                    {data: 'pickups', name: 'pickup_notes.pickups', class: 'align-middle pickups'},
                    {data: 'bookings_link', name: 'pickup_notes.bookings', class: 'align-middle bookings_link text-center'},
                    {data: 'rider', name: 'riders.name', class: 'align-middle rider'},
                    {data: 'assigned_by', name: 'ab.name', class: 'align-middle assigned_by'},
                    {data: 'assigned_date', name: 'pickup_notes.created_at', class: 'align-middle assigned_date'},
                    {data: 'completed_by', name: 'up.name', class: 'align-middle completed_by'},
                    {data: 'completed_date', name: 'pickup_notes.updated_at', class: 'align-middle completed_date'}
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

                    var pickup_select = '<select name="pickup_select" id="pickup_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(pickup_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Pickup Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data2 = $.map({!! $pickup_status !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });

                    $("#pickup_select").prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });
            var route = '{!! route('admin.tracking.index') !!}';
            $('#datatable tbody').on('click','tr td.bookings_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#bookings_modal .modal-body').html('');
                $('#bookings_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.pickups.history.bookings.all') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'pickup_note_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {

                            var shipments = '';
                            if (data.booked) {
                                $.each(data.booked, function(index, shipment_ids) {

                                    shipments += "<div><b>Shipper : "+index+"</b></div>";
                                    $.each(shipment_ids, function (index,tracking_numbers) {
                                        shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';

                                    });
                                });
                            }
                            $('#bookings_modal .modal-body').html(shipments);
                        }
                    });

            });
            $('#datatable tbody').on('click', 'tr td.pickup_note_no button.print', function() {
                var pickup_note_id = parseInt($(this).parents('tr').attr('id'));

                print(pickup_note_id);
            });
        });
    </script>
@endsection