@extends('admin.layout.master')

@section('title', 'Cargo Manifest Report')

@section('content')
    <h1 class="mb-1">
        Cargo Manifest Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <div class="col-12">
                        <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                            <div class="col-4 mt-1">
                                <fieldset class="form-group">
                                    <select name="select_origin" id="select_origin" class="form-control select2" data-rule-required="true" data-msg-required="Origin is Required">
                                            @foreach($origins as $origin)
                                            <option value="{{$origin->origin_name}}">{{$origin->origin_name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-4 mt-1">
                                <fieldset class="form-group">
                                    <select name="select_destination" id="select_destination" class="form-control select2" >
                                        @foreach($destinations as $destination)
                                            <option value="{{$destination->destination_name}}">{{$destination->destination_name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-4 mt-1">
                                <fieldset class="form-group">
                                    <select name="select_sub_segment" id="select_sub_segment" class="form-control select2">
                                        @foreach($segments as $segment)
                                            <option value="{{$segment->sub_segment_id}}" data-value="{{$segment->segment_name}}" data-value1="{{$segment->sub_segment_name}}">{{$segment->sub_segment_name}}{{' ('.$segment->segment_name.')'}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <input type="hidden" name="selected_segment_name" id="selected_segment_name">
                            <input type="hidden" name="selected_sub_segment_name" id="selected_sub_segment_name">

                            <div class="col-4 mt-1">
                                <div class="form-group input-group ">
                                    <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                    </div>
                                    <input type="text" name="search_date_from"
                                           class="form-control pickadate bg-primary border-primary white rounded-right"
                                           id="search_date_from" placeholder="Booking Date (From)" title="Booking Date (From)" data-rule-required="true" data-msg-required="Booking Date is required">
                                </div>
                            </div>
                            <div class="col-4 mt-1">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                    </div>
                                    <input type="text" name="search_date_to"
                                           class="form-control pickadate bg-primary border-primary white rounded-right"
                                           id="search_date_to" placeholder="Booking Date (To)" title="Booking Date (To)" data-rule-required="true" data-msg-required="Booking Date is required">
                                </div>
                            </div>

                            <div class="col-2 mt-1">
                                <div class="form-group">
                                    <button type="submit" id="search_filter_btn"
                                            class="btn btn-outline-info btn-min-width"><i class="la la-search"></i>
                                        Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <table class="table table-bordered datatable" id="datatable" style="width: 100%;z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1 align-middle" rowspan="2">S.No.</th>
                        <th class="border-primary border-darken-1 align-middle" rowspan="2">Booking Date.</th>
                        <th class="border-primary border-darken-1 align-middle" rowspan="2">Origin</th>
                        <th class="border-primary border-darken-1 align-middle" rowspan="2">Destination</th>
                        <th class="border-primary border-darken-1 align-middle" rowspan="2">Segments</th>
                        <th class="border-primary border-darken-1 align-middle" rowspan="2">Arrived (Up City)</th>
                        <th class="border-primary border-darken-1 align-middle" colspan="2">
                            <div class="text-center">Manifested</div>
                        </th>
                        <th class="border-primary border-darken-1 align-middle text-center" colspan="2">Without Manifest</th>
                        <th class="border-primary border-darken-1 align-middle text-center" colspan="2">Misroute</th>
                    </tr>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"># of Shipments</th>
                        <th class="border-primary border-darken-1">%age</th>
                        <th class="border-primary border-darken-1"># of Shipments</th>
                        <th class="border-primary border-darken-1">%age</th>
                        <th class="border-primary border-darken-1"># of Shipments</th>
                        <th class="border-primary border-darken-1">%age</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!--Shipments popup -->
    <div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_modal_title">Shipment(s)</h4>

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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <style>
        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
        .show_active{
            -webkit-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -moz-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        span.font-13{
            font-size: 13px;
        }
        table tfoot tr th, table.dataTable tfoot tr th {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }
    </style>

@endsection
@section('js')

    <script src="https://cdn.datatables.net/plug-ins/1.10.22/api/sum().js" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            $('#select_sub_segment').on('change', function () {
                var selectedOption = $(this).find(':selected');
                $('#selected_segment_name').val(selectedOption.data('value'));
                $('#selected_sub_segment_name').val(selectedOption.data('value1'));
            });

            $('#select_origin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Origin',
                width:'100%',
                allowClear:true
            });
            $('#select_destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Destination',
                width:'100%',
                allowClear:true
            });

            $('#select_sub_segment').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Sub Segment',
                width:'100%',
                allowClear:true
            });

            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: 'Clear',
                selectYears: true,
                selectMonths: true,
                max: '{{ Carbon\Carbon::now() }}',
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        var toDatePicker = $('#search_form #search_date_to').pickadate('picker');
                        toDatePicker.clear();
                        toDatePicker.set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));

                        // Limit the range to 30 days
                        var maxDate = new Date(context.select);
                        maxDate.setDate(maxDate.getDate() + 30);
                        toDatePicker.set('max', maxDate);
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: 'Clear',
                selectYears: true,
                selectMonths: true,
                max: '{{ Carbon\Carbon::now() }}',
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        var fromDatePicker = $('#search_form #search_date_from').pickadate('picker');
                        // fromDatePicker.set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));

                        // Limit the range to 30 days
                        var minDate = new Date(context.select);
                        minDate.setDate(minDate.getDate() - 30);
                        // fromDatePicker.set('min', minDate);
                    }
                }
            });

            // jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            //     if ( this.context.length ) {
            //         // blockPagePermanently();
            //         body = [];
            //         var params = table.ajax.params();
            //         params.start = 0;
            //         params.length = -1;
            //         params.excel = true;
            //         var jsonResult = $.ajax({
            //         {{--    url: '{{ route('admin.reports.last_mile_app.list') }}',--}}
            //         {{--    data: params,--}}
            //         {{--    success: function (result) {--}}
            //         {{--        head = [];--}}

            //         {{--        head.push('S. No');--}}
            //         {{--        head.push('Trax IDs');--}}
            //         {{--        head.push('Rider Name');--}}
            //         {{--        head.push('Hub');--}}
            //         {{--        head.push('Zone');--}}
            //         {{--        head.push('Delivery Date');--}}
            //         {{--        head.push('Total Shipments');--}}
            //         {{--        head.push('Before 11');--}}
            //         {{--        head.push('At 11');--}}
            //         {{--        head.push('At 12');--}}
            //         {{--        head.push('At 13');--}}
            //         {{--        head.push('At 14');--}}
            //         {{--        head.push('At 15');--}}
            //         {{--        head.push('At 16');--}}
            //         {{--        head.push('At 17');--}}
            //         {{--        head.push('At 18');--}}
            //         {{--        head.push('At 19');--}}
            //         {{--        head.push('At 20');--}}
            //         {{--        head.push('At 21');--}}
            //         {{--        head.push('At 22');--}}
            //         {{--        head.push('At 23');--}}
            //         {{--        head.push('After 23');--}}
            //         {{--        head.push('Total Updated Shipments');--}}
            //         {{--        head.push('Update Via App');--}}
            //         {{--        head.push('Update Via Admin');--}}
            //         {{--        $.each(result.data, function(index, values) {--}}
            //         {{--            row = [];--}}

            //         {{--            row.push(index + 1);--}}
            //         {{--            row.push(values.trax_id);--}}
            //         {{--            row.push(values.rider_name);--}}
            //         {{--            row.push(values.hub);--}}
            //         {{--            row.push(values.zone);--}}
            //         {{--            row.push(values.delivery_date);--}}
            //         {{--            row.push(values.total_shipments_excel);--}}
            //         {{--            row.push(values.before_11_count);--}}
            //         {{--            row.push(values.at_11_count);--}}
            //         {{--            row.push(values.at_12_count);--}}
            //         {{--            row.push(values.at_13_count);--}}
            //         {{--            row.push(values.at_14_count);--}}
            //         {{--            row.push(values.at_15_count);--}}
            //         {{--            row.push(values.at_16_count);--}}
            //         {{--            row.push(values.at_17_count);--}}
            //         {{--            row.push(values.at_18_count);--}}
            //         {{--            row.push(values.at_19_count);--}}
            //         {{--            row.push(values.at_20_count);--}}
            //         {{--            row.push(values.at_21_count);--}}
            //         {{--            row.push(values.at_22_count);--}}
            //         {{--            row.push(values.at_23_count);--}}
            //         {{--            row.push(values.after_23_count);--}}
            //         {{--            row.push(values.total_updated_shipments);--}}
            //         {{--            row.push(values.updated_via_rider1);--}}
            //         {{--            row.push(values.updated_via_admin1);--}}

            //         {{--            body.push(row);--}}
            //         {{--        });--}}
            //         {{--    },--}}
            //         {{--    async: false--}}
            //         });
            //         return {body: body, header: head,};
            //     }
            // });

            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Cargo Manifest Report',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                        action: function(e){
                                    if ( !$('#search_form #select_origin').val() && !$('input[name="search_date_from_formatted"]').val() && ! $('input[name="search_date_to_formatted"]').val() ) {
                                        swal({
                                            text: 'Please select Origin & Booking Dates',
                                            title: 'Validation error !',
                                            icon: 'warning',
                                            buttons: {
                                                cancel: {
                                                    text: 'OK',
                                                    value: null,
                                                    visible: true,
                                                    closeModal: true,
                                                }
                                            },
                                            dangerMode: true
                                        })
                                        return;
                                    }
                                    $.ajax({
                                        url: "{{ route('admin.reports.cargo_manifest.list') }}",
                                        method: "POST",
                                        data: {
                                            excel: true,
                                            _token: $('meta[name="csrf-token"]').attr('content'),
                                            select_origin : $('#search_form #select_origin').val(),
                                            select_destination : $('#search_form #select_destination').val(),
                                            select_segment_value : $('#search_form #selected_segment_name').val(),
                                            select_sub_segment_value : $('#search_form #selected_sub_segment_name').val(),
                                            search_date_from : $('input[name="search_date_from_formatted"]').val(),
                                            search_date_to : $('input[name="search_date_to_formatted"]').val(),
                                        },
                                        
                                        beforeSend: function() {
                                            swal({
                                                title: 'Please Wait!',
                                                text: 'Downloading is in progress',
                                                icon: 'info',
                                                buttons: false,
                                                closeOnClickOutside: false,
                                                closeOnEsc: false
                                            });
                                        },
                                        complete: function() {
                                            // Hide loader
                                            swal.close();
                                        },
                                        success: function(response) {
                                            var blob = new Blob([response], {
                                                type: 'text/csv'
                                            });
                                            var url = window.URL.createObjectURL(blob);
                                            var a = document.createElement('a');
                                            a.href = url;
                                            a.download = 'Cargo Manifest Report.csv';
                                            document.body.appendChild(a);
                                            a.click();
                                            window.URL.revokeObjectURL(url);
                                            document.body.removeChild(a);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Failed to fetch CSV data:', status, error);
                                        }
                                    });
                                }
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                autoWidth: true,
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.cargo_manifest.list') }}',
                    method: 'POST',
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    data: function (d) {
                        d.select_origin = $('#search_form #select_origin').val();
                        d.select_destination = $('#search_form #select_destination').val();
                        // d.select_sub_segment = $('#search_form #select_sub_segment').val();
                        d.select_segment_value = $('#search_form #selected_segment_name').val();
                        d.select_sub_segment_value = $('#search_form #selected_sub_segment_name').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'id',
                order: [[1, 'desc']],
                columns: [
                    {data: 'id',orderable: false, searchable: false, class: 'align-middle text-center serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'booking_date', name: 'booking_date', class: 'align-middle text-center arrived'},
                    {data: 'origin', name: 'origin', class: 'align-middle text-center'},
                    {data: 'destination', name: 'destination', class: 'align-middle text-center'},
                    {data: 'segment', name: 'segment', class: 'align-middle text-center'},
                    {data: 'arrival', name: 'arrived', class: 'align-middle text-center arrived' ,orderable: false,},
                    {data: 'manifest', class: 'align-middle text-center', orderable: false, searchable: false},
                    {data: 'manifest_percentage',  name:'manifest_percentage', class: 'align-middle text-center', orderable: false, searchable: false},
                    {data: 'withoutmanifest', class: 'align-middle text-center', orderable: false, searchable: false},
                    {data: 'withoutmanifest_percentage',  name:'withoutmanifest_percentage', class: 'align-middle text-center', orderable: false, searchable: false},
                    {data: 'misroute', name:'misroute', class: 'align-middle text-center update_via_app', orderable: false, searchable: false},
                    {data: 'misroute_percentage', name:'misroute_percentage', class: 'align-middle text-center update_via_dbf', orderable: false, searchable: false},
                ],

                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },

                drawCallback: function () {
                    var api = this.api();

                    api.rows( {page:'current'} ).every( function () {
                    } );
                },

                stateLoaded: function (settings, data) {},

                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $('#table').removeClass('d-none');
                    table.draw(true);
                }
            });

            $('#datatable_wrapper > .pull-right > .dt-buttons > a.buttons-excel').removeClass('d-none');
        });
    </script>
@endsection