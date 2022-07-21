@extends('admin.layout.master')

@section('title', 'CX Training')

@section('content')
    <h1>CX Training</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')
                            <div id="search_form" class="row mb-2 justify-content-center">
                               
                                <div class="col-3">
                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o"></span>
                                        </span>
                                        </div>
            
                                        <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Training Requested Date (From)">
                                    </div>
                                </div>
                                <div class="col-3 ">
                                    <div class="form-group input-group ml-1">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o"></span>
                                        </span>
                                        </div>
            
                                        <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Training Requested Date (To)">
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
                                        <th class="border-primary border-darken-1">Agent Name</th>
                                        <th class="border-primary border-darken-1">Unit</th>
                                        <th class="border-primary border-darken-1">Joining Date</th>
                                        <th class="border-primary border-darken-1">Requested Date</th>
                                        <th class="border-primary border-darken-1">Requested By</th>
                                        <th class="border-primary border-darken-1">Aging</th>
                                        <th class="border-primary border-darken-1">Training By</th>
                                        <th class="border-primary border-darken-1">Status Updated At</th>
                                        <th class="border-primary border-darken-1">Status</th>
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

    <div class="modal fade text-left" id="addCXTraining" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addCXTrainingModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Training Request</h4>

                </div>
                <form id="add_cx_training_form" method="post" action="{{route('admin.qa.cx_training.add')}}" class="justify-content-center" novalidate="novalidate">
                    <div class="modal-body text-center">
                        @csrf
                        <div class="form-group">
                            <select name="agent_id" class="select2" id="agent_id" data-rule-required="true" data-msg-required="Agent is required">
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="unit_id" class="select2" id="unit_id" data-rule-required="true" data-msg-required="Unit is required">
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="joining_date" class="form-control pickadate bg-primary border-primary white rounded-right" id="joining_date" placeholder="Joining Date">
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary width-100">Submit</button>
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
            $('#add_cx_training_form #joining_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                
            });


            $('#agent_id').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Agent",
                allowClear:true,
                dropdownParent:$('#add_cx_training_form')
            });

            $('#unit_id').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Unit",
                dropdownParent:$('#add_cx_training_form')
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
                        url: '{{ route('admin.qa.cx_training.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Agent Name');
                            head.push('Unit');
                            head.push('Joining Date');
                            head.push('Requested Date');
                            head.push('Requested By');
                            head.push('Aging');
                            head.push('Training By');
                            head.push('Status Updated At');
                            head.push('Status');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.agent_name);
                                row.push(values.unit);
                                row.push(values.joining_date);
                                row.push(values.requested_date);
                                row.push(values.requested_by);
                                row.push(values.aging);
                                row.push(values.training_by);
                                row.push(values.updated_at);
                                row.push(values.status_name);
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
                    @if(session('role_id') == 1 || in_array(778,session('permissions')))
                    {
                        title: 'Add ',
                        className: 'btn btn-primary',
                        text: '<i class="la la-plus"></i> Add',
                        action: function (e, dt, node, config) {
                            $('#add_cx_training_form #agent_id').val('').trigger('change');
                            $('#add_cx_training_form #unit_id').val('').trigger('change');
                            $('#add_cx_training_form #joining_date').val('');
                            $('#addCXTraining').modal('show');
                        }
                    },
                        @endif
                    {
                        extend: 'excel',
                        title: 'CX Training',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
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
                    url: '{{ route('admin.qa.cx_training.list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();

                    }
                },
                order: [[1, 'desc']],
                rowId: 'id',
                columns: [
                   
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return ''; }},
                    { data: 'agent_name', name: 'admins.name', class: 'align-middle agent_name'},
                    { data: 'unit', name: 'ctu.name', class: 'align-middle unit'},
                    { data: 'joining_date', name: 'cx_trainings.joining_date', class: 'align-middle joining_date'},
                    { data: 'requested_date', name: 'cx_trainings.requested_date', class: 'align-middle requested_date'},
                    { data: 'requested_by', name: 'rb.name', class: 'align-middle requested_by'},
                    { data: 'aging', name: 'aging', class: 'align-middle aging'},
                    { data: 'training_by', name: 'tb.name', class: 'align-middle training_by'},
                    { data: 'updated_at', name: 'cx_trainings.updated_at', class: 'align-middle updated_at'},
                    { data: 'status_name', name: 'cx_trainings.status', class: 'align-middle status_name', orderable: false, searchable: false},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    
                    // console.log($('tr', row));
                    console.log(row);
                    if(data.aging == 2 && data.status == 1){
                        row.classList.add('bg-danger')
                        row.classList.add('text-white')
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="1">Requested</option>' +
                        '<option value="2">Inprocess</option>' +
                        '<option value="3">Completed</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action')  || $(header).is('.serial_number') || $(header).is('.aging') || $(header).is('.agent_name') || $(header).is('.unit') || $(header).is('.joining_date') || $(header).is('.requested_date') || $(header).is('.requested_by') || $(header).is('.training_by') || $(header).is('.updated_at') || $(header).is('.status_name')) {
                            $(td).appendTo($(search));
                        }
                        
                        // else if($(header).is('.status_name')){
                        //     $(status_select).appendTo($(search))
                        //         .on( 'change', function () {
                        //             column.search($(this).val(), false, false, true).draw();
                        //         } ).wrap(td);
                        // }
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
            $('#add_cx_training_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    form.submit();

                    // swal({
                    //     title: 'Are You Sure?',
                    //     text: 'You want to mark this Shipper as High Alert!',
                    //     icon: 'warning',
                    //     buttons: {
                    //         cancel: {
                    //             text: 'No',
                    //             value: null,
                    //             visible: true,
                    //             closeModal: true,
                    //         },
                    //         confirm: {
                    //             text: 'Yes',
                    //             value: true,
                    //             visible: true,
                    //             closeModal: true
                    //         }
                    //     },
                    //     closeOnClickOutside: false,
                    //     closeOnEsc: false,
                    //     dangerMode: true
                    // }).then(function (confirm) {
                    //     if (confirm) {
                            
                    //     }
                    // });

                }
            });

            $('#datatable tbody').on('click', 'td.action button', function (){
                if($(this).hasClass('update_status')){
                    console.log('aa');
                    var id = $(this).parents('tr').attr('id');
                    if(id){

                        $.ajax({
                            url: '{!! route('admin.qa.cx_training.update_status') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'id': id,
                            }
                        }).done(function (data) {
                            if(data.status == 0){
                                toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                            }
                            else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            table.draw();
                        });
                    }
                }
                if($(this).hasClass('remove')){
                    var id = $(this).parents('tr').attr('id');
                    if(id){
                        swal({
                            title: 'Are You Sure?',
                            text: 'You want to mark this Shipper as High Alert!',
                            icon: 'warning',
                            buttons: {
                                cancel: {
                                    text: 'No',
                                    value: null,
                                    visible: true,
                                    closeModal: true,
                                },
                                confirm: {
                                    text: 'Yes',
                                    value: true,
                                    visible: true,
                                    closeModal: true
                                }
                            },
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true
                        }).then(function (confirm) {
                            if (confirm) {
                                $.ajax({
                                    url: '{!! route('admin.qa.high_alert.shippers.remove') !!}',
                                    method: 'POST',
                                    data: {
                                        '_token': '{{ csrf_token() }}',
                                        'alert_id': id,
                                    }
                                }).done(function (data) {
                                    if(data.status == 0){
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                        table.draw(false);
                                    }
                                    else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }
                                });
                            }
                        });


                    }
                }

            });


            $('#edit_shipper_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'You want to mark this Shipper as High Alert!',
                        icon: 'warning',
                        buttons: {
                            cancel: {
                                text: 'No',
                                value: null,
                                visible: true,
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Yes',
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    }).then(function (confirm) {
                        if (confirm) {

                            var alert_id = $('#edit_shipper_form #alert_id').val();
                            // var shipper_id = $('#edit_shipper_form #edit_shipper_select').val();
                            var description = $('#edit_shipper_form #edit_description').val();
                            $.ajax({
                                url: '{{ route('admin.qa.high_alert.shippers.edit') }}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    // 'shipper_id': shipper_id,
                                    'alert_id': alert_id,
                                    'description': description,
                                }
                            })
                                .done(function (data){
                                    if(data.status == 0){
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                        $('#editShipperModal').modal('hide');
                                        table.draw(false);

                                    }
                                    else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }
                                    $('#addShipperModal').modal('hide');
                                    table.draw(false);
                                });
                        }
                    });

                }
            });
        });
    </script>

@endsection