@extends('admin.layout.master')
@section('title','Geocodes Settings')

@section('content')
    <style>
        /* Dim the row slightly to show it's geocoded */
        td.select-checkbox.geo-checkbox-disabled {
            pointer-events: none;         /* 🔒 Prevents clicks */
            opacity: 0.5;                 /* Optional: faded look */
            cursor: not-allowed;          /* Show disabled cursor */
        }



    </style>
    <h1 class="mb-1">
        Geocodes Settings
    </h1>
    <!-- Geo Code Loader -->
    <div id="geo-code-loader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:9999; background:rgba(255,255,255,0.7); text-align:center;">
        <div style="position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%);">
            <span class="spinner-border text-primary" role="status"></span>
            <div style="margin-top:10px;">Generating Geo Code...</div>
        </div>
    </div>
    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
{{--                <form id="geo_codes_search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">--}}
                    <div class="row w-100">
                        <div class="col-md-3 mt-2">

                        <div class="form-group">
                            <input type="text" id="tracking_number" name="tracking_numbers" class="dt_search tracking_numbers form-inline mb-1 justify-content-center"
                                   placeholder="Tracking Number(s)" data-tags-input-name="tracking_number">
                        </div>
{{--                            <input type="text" name="tracking_numbers" class=" dt_search form-control w-100 tracking_numbers"--}}
{{--                                   placeholder="Tracking Number(s)" data-tags-input-name="tracking_number">--}}
{{--                            <input type="text" name="tracking_numbers" class="form-control tracking_numbers" placeholder="Tracking Number(s)" data-tags-input-name="tracking_number" tabindex="-1" >--}}
                        </div>

                        <div class="col-md-3 mt-2">
                            <select name="origin_id" id="origin_id" class="form-control select2 w-100">
{{--                                <option value="">Select Origin</option>--}}
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" > {{ $city->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mt-2">
                            <select name="destination_id" id="destination_id" class="form-control select2 w-100">
                                <option value="">Select Destination</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" > {{ $city->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mt-2">
                            <select name="account_type_id" id="account_type_id" class="form-control select2 w-100">
                                <option value="">Account Type</option>
                                @foreach($account_types as $account_type)
                                    <option value="{{ $account_type->id }}" > {{ $account_type->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mt-2">
                            <select name="region_id" id="region_id" class="form-control select2 w-100">
                                <option value="">Select Region</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" > {{ $region->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mt-2">
                            <select name="segment_id" id="segment_id" class="form-control select2 w-100">
                                <option value="">Select Segment</option>
                                @foreach($segments as $segment)
                                    <option value="{{ $segment->id }}" > {{ $segment->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mt-2">
                            <select name="sub_segment_id" id="sub_segment_id" class="form-control select2 w-100">
                            </select>
                        </div>

                        <div class="col-md-3 mt-2">
                            <button id="search_filter_btn" type="submit" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i
                                        class="la la-search"></i> Search
                            </button>
                        </div>

                    </div>
{{--                </form>--}}

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Consignee Address</th>
                        <th class="border-primary border-darken-1">Consignee Number</th>
                        <th class="border-primary border-darken-1">Latitude</th>
                        <th class="border-primary border-darken-1">Longitude</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

{{--    <div class="modal fade text-left" id="StatusModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="StatusModal"--}}
{{--         aria-hidden="true">--}}
{{--        <div class="modal-dialog modal-sm" role="document">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header bg-primary white">--}}
{{--                    <h4 class="modal-title white">Add Closing Status</h4>--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <div class="modal-body  text-center">--}}
{{--                    <div class="row mb-2 justify-content-center">--}}
{{--                        <div class="col-12 form-group">--}}
{{--                            <input name="status_name" id="status_name" class="form-control status_name" placeholder="Enter closing type name">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row justify-content-center">--}}
{{--                        <div class="col-12">--}}
{{--                            <button id="addStatus" type="button" class="btn btn-primary btn-block">Add</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    --}}{{--edit--}}
{{--    <div class="modal fade text-left" id="EditStatusModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditStatusModal"--}}
{{--         aria-hidden="true">--}}
{{--        <div class="modal-dialog modal-sm" role="document">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header bg-primary white">--}}
{{--                    <h4 class="modal-title white">Edit Closing Status</h4>--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <input type="hidden" id="edit_closing_status_id">--}}
{{--                <div class="modal-body  text-center">--}}
{{--                    <div class="row mb-2 justify-content-center">--}}
{{--                        <div class="col-12 form-group">--}}
{{--                            <input name="edit_closing_type" id="edit_closing_type" class="form-control edit_closing_type" placeholder="Enter closing type">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row justify-content-center">--}}
{{--                        <div class="col-12">--}}
{{--                            <button id="edit_type" type="button" class="btn btn-primary btn-block">Edit</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

    <style type="text/css">
        /*.geo-selected-row {*/
        /*    background-color: #d1f0d1 !important; !* light green for example *!*/
        /*}*/
        .selectize-control {
            width:  100%  !important;
        }
    </style>

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            $('#origin_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Origin',
                width: '100%',
                allowClear: true
            });
            $('#destination_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Destination',
                width: '100%',
                allowClear: true
            });

            $('#region_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Region',
                width: '100%',
                allowClear: true
            });

            $('#account_type_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Account type',
                width: '100%',
                allowClear: true
            });

            $('#segment_id').select2({
                width:'100%',
                placeholder:"Select Segments",
                allowClear:false,
                // dropdownParent:$('#geo_codes_search_form')
            }).bind('change', function() {
                var business_segment_id = $(this).val();
                $('#sub_segment_id').attr('disabled','disabled');
                $('#sub_segment_id').empty();
                $.ajax({
                    url:'{!! route("admin.settings.auto_assigning.get_sub_segments") !!}',
                    method: 'POST',
                    data: {
                        'business_segment_id': [business_segment_id],
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status == 1){
                        $('#sub_segment_id').removeAttr('disabled');
                        let options = `<option value="">Select Sub Segment</option>`;
                        $.each(data.sub_segment, function(index, field) {
                            options+=`<option value='${field.id}' >${field.name}</option>`;
                        });
                        $('#sub_segment_id').append(options);
                        $('#sub_segment_id').find('option').filter(function() {
                            return $.trim($(this).text()) === '';
                        }).remove();
                    }
                })
            });

            // $('#segment_id').prepend('<option value="" selected="selected"></option>').select2({
            //     placeholder: 'Select Segment',
            //     width: '100%',
            //     allowClear: true
            // });



            $('#sub_segment_id').select2({
                width:'100%',
                placeholder:"Select Sub Segment",
                allowClear: true,
            });

            $('#search_filter_btn').on('click',function () {
                var origin_id = $("#origin_id").val();
                var destination_id = $("#destination_id").val();
                var account_type_id = $("#account_type_id").val();
                var region_id = $("#region_id").val();
                var segment_id = $("#segment_id").val();
                var sub_segment_id = $("#sub_segment_id").val();
                var tracking_number = $("#tracking_number").val();
                if( !origin_id && !destination_id && !account_type_id &&
                    !region_id && !segment_id && !sub_segment_id &&
                    !tracking_number){
                    toastr.error('Please Select at least one filter', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                } else {
                    table.draw();
                }

            });

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    var body = [];
                    var head = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.geo_codes.list') }}',
                        data: params,
                        success: function (result) {
                            head = [
                                'S.No',
                                'Tracking Number',
                                'Consignee Name',
                                'Consignee Address',
                                'Consignee Number',
                                'Latitude',
                                'Longitude'
                            ];

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.consignee_name);
                                row.push(values.consignee_address);
                                row.push(values.consignee_phone_number_1);
                                row.push(values.latitude);
                                row.push(values.longitude);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });


            let selected_rows_geocord_array = [];
            let selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[
                    {
                        text: 'View Map',
                        className: 'btn btn-primary view_geo_map_btn',
                        enabled: false,
                        action: function () {
                            if (selected_rows.length > 0) {
                                const encoded = btoa(JSON.stringify(selected_rows));
                                window.open('/admin/settings/geo_codes/view_tpl_map?coords=' + encoded, '_blank');
                            } else {
                                toastr.error('No geo-coded shipments selected!');
                            }
                        }
                    },

                        @if (session('role_id') == 1 || in_array(1041, session('permissions')))
                             {
                                text: 'Generate Geo Codes',
                                className: 'btn btn-primary generate_geo_codes',
                                enabled: false,
                                action: function (e, dt, node, config) {
                                    if(selected_rows_geocord_array.length > 0) {
                                        get_shipments_lat_long(selected_rows_geocord_array);
                                    }

                                }
                             },

                        @endif

                    {
                        extend: 'selectAll',
                        text: 'Select All Lat/Long',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            // 🔁 Clear Geo Code selection first

                            table.rows().deselect();

                            table.button('.view_geo_map_btn').disable();

                            selected_rows = [];

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                var latCell = $(row.node()).find('td.latitude').text().trim();
                                var lngCell = $(row.node()).find('td.longitude').text().trim();

                                table.button('.generate_geo_codes').disable();

                                // ✅ Change: select rows that HAVE lat & lng
                                if (latCell && lngCell) {
                                    if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                        row.select();
                                        var id = parseInt(row.id());
                                        if (!selected_rows.includes(id)) {
                                            selected_rows.push(id);
                                        }

                                        if (selected_rows.length > 0) {
                                            table.button('.generate_geo_codes').disable();
                                            table.button('.select_none').enable();
                                            table.button('.view_geo_map_btn').enable();
                                        }else{
                                            table.button('.view_geo_map_btn').disable();

                                        }
                                    }
                                }
                            });

                        }
                    },
                    {
                        extend: 'selectNone',
                        text: 'Select None Lat/Long',
                        className: 'select_none',
                        action: function (e) {
                            e.preventDefault();

                            // Deselect only rows that were selected by Select All (i.e., with lat & lng)
                            table.rows().nodes().each(function(index) {
                                const row = table.row(index);
                                const rowNode = row.node();

                                const latCell = $(rowNode).find('td.latitude').text().trim();
                                const lngCell = $(rowNode).find('td.longitude').text().trim();

                                if (latCell && lngCell) {
                                    row.deselect(); // Deselect lat/lng rows
                                }
                            });

                            // Clear the selected_rows array
                            selected_rows = [];

                            // Disable buttons
                            table.button('.view_geo_map_btn').disable();
                            table.button('.select_none').disable();
                            table.button('.generate_geo_codes').disable();
                            table.button('.select_all').enable(); // optional, in case you want to allow selecting again
                        }
                    },



                    {
                        extend: 'excel',
                        title: 'Shipment Goecodes',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'],
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                deferLoading: 0,
                scrollY:'300px',
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000], [50, 100, 500, 1000]],
                pageLength: 50,
                pagingStatus: 'full_numbers',
                ajax: {
                    url: '{{ route('admin.settings.geo_codes.list')}}',
                    data: function (d) {
                        d.tracking_number =  $("#tracking_number").val();
                        d.origin_id =        $("#origin_id").val();
                        d.destination_id =   $("#destination_id").val();
                        d.account_type_id =  $("#account_type_id").val();
                        d.region_id =        $('#region_id').val();
                        d.segment_id =       $('#segment_id').val();
                        d.sub_segment_id =   $('#sub_segment_id').val();
                    }
                },
                rowId: 'id',
                order: [1, 'desc'],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0,orderable: false, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0,orderable: false,render: function (data, type, row) {return '';}},
                    {data: 'tracking_number', name: 'tracking_number', class: 'align-middle tracking_number',orderable: false},
                    {data: 'consignee_name', name: 'consignee_name', class: 'align-middle consignee_name',searchable:true,orderable: false},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address',searchable:true,orderable: false},
                    {data: 'consignee_phone_number_1', name: 'shipments.consignee_phone_number_1', class: 'align-middle consignee_phone_number_1',searchable:true,orderable: false},
                    {data: 'latitude', name: 'sgc.latitude', class: 'align-middle latitude',searchable:true,orderable: false},
                    {data: 'longitude', name: 'sgc.longitude', class: 'align-middle longitude',searchable:true,orderable: false},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                },
                createdRow:function (row, data, dataIndex) {


                    var lat = $(row).find('td.latitude').text().trim();
                    var lng = $(row).find('td.longitude').text().trim();

                    // Add a marker class if lat/lng exists
                    if (lat && lng) {
                        // Add a marker class only to the checkbox cell
                        $(row).find('td.select-checkbox').addClass('geo-checkbox-disabled');
                    }
                //     // const $cell = $('td', row).eq(0); // first cell
                //     //
                //     // if (!data.latitude || !data.longitude) {
                //     //     // Selectable row
                //     //     $cell.addClass('geo-select-check'); // separate name
                //     //     $cell.css({ opacity: 0.5,}); // visually disabled
                //     // } else {
                //     //     // Non-selectable, visually similar
                //     //     $cell.addClass('geo-select-check'); // separate name
                //     //     $cell.css({ opacity: 0.5,}); // visually disabled
                //     // }
                //     // // if (data.latitude && data.longitude) {
                //     // //     $cell.removeClass('select-checkbox'); // remove selectable behavior
                //     // //     $cell.addClass('text-muted'); // optional: gray out
                //     // //     $cell.html('&#10005;');       // optional: show X or dash
                //     // // } else {
                //     // //     $cell.addClass('select-checkbox'); // allow selection
                //     // // }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).hasClass('action') || $(header).hasClass('select') || $(header).hasClass('serial_number')) {
                            $(search).append('<td style="padding:5px;" class="border-primary border-lighten-2"></td>');
                        } else {
                            var td = $('<td style="padding:5px;" class="border-primary border-lighten-2"></td>');
                            var inputGroup = $('<fieldset class="form-group m-0 position-relative has-icon-right"></fieldset>');
                            var input = $('<input type="text" class="form-control form-control-sm input-sm primary">');
                            var icon = $('<div class="form-control-position primary"><i class="la la-search"></i></div>');

                            input.on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            });

                            if (column.search()) {
                                input.val(column.search());
                            }

                            inputGroup.append(input).append(icon);
                            td.append(inputGroup);
                            $(search).append(td);
                        }

                    });

                    this.api().table().columns.adjust();
                }
            });


            $('.datatable tbody').on('click', 'tr td.select-checkbox', function () {
                var $row = $(this).closest('tr');
                var id = parseInt($row.attr('id'));
                 $('.select_none').click();

                setTimeout(function () {
                    var lat = $row.find('td.latitude').text().trim();
                    var lng = $row.find('td.longitude').text().trim();

                    if ($row.hasClass('selected')) {
                        // Row was selected
                        if (!lat || !lng) {
                            if (!selected_rows_geocord_array.includes(id)) {
                                selected_rows_geocord_array.push(id);
                            }
                        }
                    } else {
                        // Row was deselected
                        selected_rows_geocord_array = selected_rows_geocord_array.filter(val => val !== id);
                    }

                    // ✅ Enable/disable button based on selected rows
                    if (selected_rows_geocord_array.length > 0) {
                        table.button('.generate_geo_codes').enable();
                    } else {
                        table.button('.generate_geo_codes').disable();
                    }
                }, 100);
            });





            $('body').on('click','#datatable tr .generate_geo_code_btn',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id){
                   get_shipments_lat_long([id]);
                }
                else{
                    var error = 'Shipment ID Not Found, Please Try again!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });

            function get_shipments_lat_long(shipment_ids) {
                $('#geo-code-loader').show();
                table.button('.generate_geo_codes').disable();
                $.ajax({
                    url: '{!! route('admin.settings.geo_codes.get_shipment_lat_long') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'shipment_ids': shipment_ids
                    }
                }).done(function(data){
                    if(data.status){
                        table.rows().deselect();
                        table.draw(true);
                        selected_rows_geocord_array=[];
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    } else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }).fail(function () {
                    toastr.error('Something went wrong while contacting the server.', 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }).always(function () {
                    // Hide loader in both success and fail cases
                    $('#geo-code-loader').hide();
                });

            }

            var select = $('.tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function(dropdown) {
                    dropdown.remove();
                },
                onType: function(str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function(input) {
                    if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                },
            });
        });


    </script>
@endsection