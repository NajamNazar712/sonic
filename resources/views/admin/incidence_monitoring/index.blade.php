@extends('admin.layout.master')

@section('title', 'Incidence Monitoring')

@section('content')
    <h1>Incidence Monitoring</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')
                            <div class="row mb-2 justify-content-center">

                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <select name="search_zone" id="search_zone" class="form-control select2">
                                            @foreach($zones as $zone)
                                                <option value="{{$zone->id}}">{{$zone->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                 <div class="col-4">
                                     <fieldset class="form-group">
                                     <select name="search_hub" id="search_hub" class="form-control select2">
                                         @foreach($hubs as $hub)
                                             <option value="{{$hub->id}}">{{$hub->name}}</option>
                                         @endforeach
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
                                         <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-value="">
                                     </div>
                                 </div>
                                 <div class="col-4">
                                     <div class="form-group input-group">
                                         <div class="input-group-prepend">
                                         <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                             <span class="la la-calendar-o"></span>
                                         </span>
                                         </div>
                                         <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-value="">
                                     </div>
                                 </div>
                                 
                                 <div class="col-2">
                                     <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                 </div>
                             </div>
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">Station</th>
                                    <th class="border-primary border-darken-1">Case#</th>
                                    <th class="border-primary border-darken-1">Monitoring Area</th>
                                    <th class="border-primary border-darken-1">Time Slot</th>
                                    <th class="border-primary border-darken-1">Case Nature</th>
                                    <th class="border-primary border-darken-1">Observations</th>
                                    <th class="border-primary border-darken-1">NC Level</th>
                                    <th class="border-primary border-darken-1">Tagged To</th>
                                    <th class="border-primary border-darken-1">Tagging Date</th>
                                    <th class="border-primary border-darken-1">Current Status</th>
                                    <th class="border-primary border-darken-1">Clips Link</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade text-left" id="addReportModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addReportModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Report</h4>

                </div>
                <form id="add_report_form" method="post" action="{{route('admin.incidence_monitoring.add')}}" class="justify-content-center" novalidate="novalidate">
                    <div class="modal-body text-center">
                        @csrf
                        <div class="form-group">
                            <select class="form-control" name="station_id" id="station" data-rule-required="true" data-msg-required="Station is required">
                                @foreach($stations as $station)
                                    <option value="{{$station->id}}">{{$station->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="monitoring_area_id" id="monitoring_area" data-rule-required="true" data-msg-required="Monitoring Area is required">
                                @foreach($monitoring_areas as $monitoring_area)
                                    <option value="{{$monitoring_area->id}}">{{$monitoring_area->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <p class="mt-1">Time From:</p>
                                </span>
                            </div>

                            <input type="time" name="time_from"  class="form-control pickadate bg-primary border-primary white rounded-right deposit_date" id="time_from" placeholder="Time*" data-rule-required="true" data-msg-required="Time is required">
                        </div>


                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <p class="mt-1">Time To:</p>
                                </span>
                            </div>

                            <input type="time" name="time_to"  class="form-control pickadate bg-primary border-primary white rounded-right deposit_date" id="time_to" placeholder="Time*" data-rule-required="true" data-msg-required="Time is required">
                        </div>

                        <div class="form-group">
                            <select class="form-control" name="case_nature_id" id="case_nature" data-rule-required="true" data-msg-required="Case Nature is required">
                                @foreach($case_natures as $case_nature)
                                    <option value="{{$case_nature->id}}">{{$case_nature->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" name="observations" id="observations" placeholder="Enter Observations" data-rule-required="true" data-msg-required="Observations is required">
                        </div>

                        <div class="form-group">
                            <select class="form-control" name="nc_level_id" id="nc_level" data-rule-required="true" data-msg-required="NC Level is required">
                                @foreach($nc_levels as $nc_level)
                                    <option value="{{$nc_level->id}}">{{$nc_level->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control select2" name="tagged_to[]"  multiple="multiple" id="tagged_to" data-rule-required="true" data-msg-required="Select Manager is required">
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" name="clip_link" placeholder="Enter Clip Link" id="clip_link" data-rule-required="true" data-msg-required="Clip Link To is required">
                        </div>
                        
                      


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary width-100" id="add_special_rider_button">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/textarea/autosize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {


            
            $('#search_zone').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Search Zone",
                allowClear:true,
            });
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Search Hub",
                allowClear:true,
            });
            $('#from_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#to_date').pickadate('picker').set('min', $('#from_date').pickadate('picker').get('select'));
                    }
                }
            });
            $('#to_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#from_date').pickadate('picker').set('max', $('#to_date').pickadate('picker').get('select'));
                    }
                }
            });
            $('#tagged_to').select2({
                width:'100%',
                placeholder:"Tag Person*",
                allowClear:true,
                dropdownParent:$('#add_report_form')
            });
            $('#station').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Select Station *',
                dropdownParent:$('#add_report_form')
			}).bind('change', function(asd) {
                console.log(asd);
                $.ajax({
                        
                        url: '{!! route('admin.incidence_monitoring.get_managers') !!}',
                        method: 'POST',
                        data: {
                            'hub_id': $(this).val(),
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {
                            console.log(data);
                            if(data.status){
                                $.each(data.agents, function (index, agent) {
                                    $('#tagged_to').append('<option value="'+agent.id+'" >'+agent.name+'</option>')
                                });
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            
                        });
				
			});
            $('#monitoring_area').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Monitoring Area',
                width:'100%',
                allowClear:true,
                dropdownParent:$('#add_report_form')
            });
            $('#case_nature').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Case Nature',
                width:'100%',
                allowClear:true,
                dropdownParent:$('#add_report_form')
            });
            $('#nc_level').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select NC Level',
                width:'100%',
                allowClear:true,
                dropdownParent:$('#add_report_form')
            });
            $('#add_report_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
            });
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.incidence_monitoring.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Station');
                            head.push('Case #');
                            head.push('Monitoring Area');
                            head.push('Time Slot');
                            head.push('Case Nature');
                            head.push('Observations');
                            head.push('NC Level');
                            head.push('Tagged To');
                            head.push('Tagging Date');
                            head.push('Current Status');
                            head.push('Clips Link');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.req_id);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.tracking_number);
                                row.push(values.weight);
                                row.push(values.vehicle);
                                row.push(values.quantity);
                                row.push(values.date);
                                row.push(values.updated_on);
                                row.push(values.updated_by);
                                row.push(values.status);
                                row.push(values.vendor);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    @if(session('role_id') == 1 || in_array(536,session('permissions')))
                    {
                        text: 'Add Report',
                        className: 'btn btn-primary',
                        action: function (e, dt, node, config) {
                            $('#addReportModal').modal('show');
                        }
                    },
                    @endif
                    {
                        extend: 'excel',
                        title: 'Incidence Monitoring',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                autoWidth: false,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,

                ajax: {
                    url: '{{ route('admin.incidence_monitoring.list') }}',
                    data : function (d) {
                        d.search_hub = $('#search_hub').val();
                        d.search_zone = $('#search_zone').val();
                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                order: [[9, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return ''; }},
                    {data: 'station_name', name: 'station.id', class: 'align-middle station_name'},
                    {data: 'incidence_monitorings_id_padded', name: 'incidence_monitorings.id', class: 'align-middle incidence_monitorings_id_padded'},
                    {data: 'area_name', name: 'area.name', class: 'align-middle area_name'},
                    {data: 'time_slot', name: 'time_slot', class: 'align-middle time_slot', orderable: false, searchable: false},
                    {data: 'case_nature_type', name: 'case_nature.name', class: 'align-middle case_nature_type'},
                    {data: 'observation', name: 'incidence_monitorings.observation', class: 'align-middle observation'},
                    {data: 'nc_level_name', name: 'nc_level.name', class: 'align-middle nc_level_name'},
                    {data: 'tagged_to', name: 'tagged_to', class: 'align-middle tagged_to', orderable: false, searchable: false},
                    {data: 'tagging_date', name: 'incidence_monitorings.tagging_date', class: 'align-middle tagging_date'},
                    {data: 'status_name', name: 'status.status', class: 'align-middle status_name'},
                    {data: 'clip_link', name: 'incidence_monitorings.clip_link', class: 'align-middle clip_link'},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action')  || $(header).is('.serial_number') || $(header).is('.tagged_to') || $(header).is('.time_slot')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
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
                    this.api().table().columns.adjust();
                }
            });
            $('#search_filter_btn').on('click',function () {
               table.draw();
            });

        });
    </script>

@endsection