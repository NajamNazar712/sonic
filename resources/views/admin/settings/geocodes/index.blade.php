@extends('admin.layout.master')
@section('title','Geocodes Settings')

@section('content')
    <h1 class="mb-1">
        Geocodes Settings
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
{{--                <form id="geo_codes_search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">--}}
                    <div class="row w-100">
                        <div class="col-md-3 mt-2">
                            <input type="text" name="tracking_numbers" class="form-control w-100 tracking_numbers"
                                   placeholder="Tracking Number(s)" data-tags-input-name="tracking_number">
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
                                <option value="">Select Sub Segment</option>
                                {{-- @foreach logic --}}
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

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

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
                        let options = "";
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

            $('#sub_segment_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Sub Segment',
                width: '100%',
                allowClear: true
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

            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[
                    {
                        text: 'Generate Geo Codes',
                        className: 'btn btn-primary generate_geo_codes',
                        enabled: false,
                        action: function (e, dt, node, config) {
                           get_shipments_lat_long();
                        }
                    },
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.generate_geo_codes').enable();

                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.generate_geo_codes').disable();
                                    }
                                }
                            });
                        }
                    },
                    'reset'],
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                scrollY:'300px',
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingStatus: 'full_numbers',
                ajax: '{{ route('admin.settings.geo_codes.list') }}',
                rowId: 'id',
                order: [1, 'desc'],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number', name: 'tracking_number', class: 'align-middle tracking_number'},
                    {data: 'consignee_name', name: 'consignee_name', class: 'align-middle consignee_name',searchable:true},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address',searchable:true},
                    {data: 'consignee_phone_number_1', name: 'shipments.consignee_phone_number_1', class: 'align-middle consignee_phone_number_1',searchable:true},
                    {data: 'latitude', name: 'sgc.latitude', class: 'align-middle latitude',searchable:true},
                    {data: 'longitude', name: 'sgc.longitude', class: 'align-middle longitude',searchable:true},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

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

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = $(this).parent('tr').attr('id');
                console.log(id);

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.generate_geo_codes').enable();
                }
                else {
                    table.button('.generate_geo_codes').disable();
                }
            });
            // $('body').on('click','#datatable button.edit',function () {
            //     var id = parseInt($(this).parents('tr').attr('id'));
            //     var status_name = $(this).parents('tr').find('td.name').text();
            //     if(id){
            //         $('#edit_closing_status_id').val(id);
            //         $('#EditStatusModal').modal('show');
            //         $('#edit_closing_type').val(status_name);
            //     }else{
            //         var error = 'Status ID Not Found, Please Try again!';
            //         toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            //     }
            // });
            // $('body').on('change','#StatusModal #status_name,#EditStatusModal #edit_closing_type',function() {
            //     $(this).val($(this).val().trim());
            // });
            {{--$('body').on('click','#addStatus', function () {--}}
            {{--    var status = $('#status_name').val();--}}
            {{--    if(status != ''){--}}
            {{--        $.ajax({--}}
            {{--            url: '{!! route('admin.settings.month_closing.status.add') !!}',--}}
            {{--            method: 'POST',--}}
            {{--            data: {--}}
            {{--                '_token': '{{ csrf_token() }}',--}}
            {{--                'status': status--}}
            {{--            }--}}
            {{--        }).done(function(data){--}}
            {{--            if(data.status){--}}
            {{--                table.draw(true);--}}
            {{--                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
            {{--                $('#status_name').val('');--}}
            {{--                $('#StatusModal').modal('hide');--}}
            {{--            }else{--}}
            {{--                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--            }--}}
            {{--        });--}}

            {{--    }else{--}}
            {{--        var error = 'Month Closing Status is empty!';--}}
            {{--        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--    }--}}
            {{--});--}}
            {{--$('body').on('click','#edit_type', function () {--}}
            {{--    var status = $('#edit_closing_type').val();--}}
            {{--    var id = parseInt($('#edit_closing_status_id').val());--}}
            {{--    if(status != '' && id != ''){--}}
            {{--        $.ajax({--}}
            {{--            url: '{!! route('admin.settings.month_closing.status.edit') !!}',--}}
            {{--            method: 'POST',--}}
            {{--            data: {--}}
            {{--                '_token': '{{ csrf_token() }}',--}}
            {{--                'status_id': id,--}}
            {{--                'status_name':status--}}
            {{--            }--}}
            {{--        }).done(function(data){--}}
            {{--            if(data.status){--}}
            {{--                table.draw(true);--}}
            {{--                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
            {{--                $('#edit_closing_type').val('');--}}
            {{--                $('#EditStatusModal').modal('hide');--}}
            {{--            }--}}
            {{--        });--}}

            {{--    }else{--}}
            {{--        var error = 'Month Closing Status is empty!';--}}
            {{--        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--    }--}}
            {{--});--}}

        });

        function get_shipments_lat_long() {
            alert(1);
        }
    </script>
@endsection