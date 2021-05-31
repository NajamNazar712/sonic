@extends('admin.layout.master')

@section('title', 'Fleet Management')

@section('content')
    <h1 class="mb-1">
        Fleet Management
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Registration Number</th>
                        <th class="border-primary border-darken-1">Vehicle Type</th>
                        <th class="border-primary border-darken-1">Tracking ID</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="AddFleetModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddFleetModal"
    aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
       <div class="modal-content">
           <div class="modal-header bg-primary white">
               <h4 class="modal-title white">Add Fleet</h4>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">&times;</span>
               </button>
           </div>
           <form id="fleet_add_form" class="form-horizontal" action="{{ route('admin.settings.fleet.store') }}" method="POST" novalidate="novalidate">
               @csrf
               <div class="modal-body">
                   <div class="row justify-content-center">
                       <div class="col-12 form-group">
                           <input type="text" name="reg_number" id="reg_number" class="form-control reg_number" placeholder="Registration Number*" data-rule-required="true" data-msg-required="Registration Number is required" data-rule-remote="{{ route('admin.settings.fleet.unique') }}" data-msg-remote="Registration Number must be unique">
                       </div>
                   </div>
                   <div class="row justify-content-center">
                       <div class="col-12 form-group">
                        <select class="form-control select2" id="vehicle_select" name="vehicle_select" data-rule-required="true" data-msg-required="Vehicle Type is required">
                            @foreach($vehicles as $vehicle)
                                <option value="{{$vehicle->id}}">{{$vehicle->name}}</option>
                            @endforeach
                        </select>
                       </div>

                       <div class="col-12 form-group d-none" id="other_picker_name_div">
                        <div class="form-group col-md">
                            <input type="text" name="vehicle_type_name" id="vehicle_type_name" class="form-control" placeholder="New Vehicle Type" data-rule-required="true" data-msg-required="Vehicle Type is required">
                        </div>
                        </div>
                   </div>
                   <div class="row justify-content-center">
                        <div class="col-12 form-group">
                            <input type="text" name="tracking_id" id="tracking_id" class="form-control tracking_id" placeholder="Tracking ID*" data-rule-required="true" data-msg-required="Tracking ID is required">
                        </div>
                    </div>
                   
               </div>
               <div class="modal-footer">
                   <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                   <button id="AddFleetBtn" type="submit" class="btn btn-info">Add</button>
               </div>
           </form>
       </div>
   </div>
</div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <style type="text/css">
        table.dataTable {
            font-size: 12px;
        }

        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
            word-wrap: break-word;
        }

        table.dataTable thead tr th:before,
        table.dataTable thead tr th:after {
            height: 20px;
            margin-bottom: -10px;
            bottom: 50% !important;
        }

        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            $('#vehicle_select').prepend('<option value="" selected="selected"></option>').append('<option value="other">Other</option>').select2({
                width: '100%',
                placeholder: 'Select Vehicle Type*',
                dropdownParent:$('#fleet_add_form')
            }).bind('change', function() {
                if ($(this).val() === 'other') {
                    $('#other_picker_name_div').removeClass('d-none');
                }
                else{
                    $('#other_picker_name_div').addClass('d-none');
                }
            });


            // $("#runner").prepend('<option value="" selected></option>').select2({
            //     placeholder: "Select Runner",
            //     width:'100%',
            //     dropdownParent:$('#SelectRunnerModal')
            // });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.fleet.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Registration Number');
                            head.push('Vehicle Type');
                            head.push('Tracking ID');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.reg_number);
                                row.push(values.vehicle_type);
                                row.push(values.tracking_id);
                                row.push(values.status);

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
                scrollX: true, scrollY: '500px',
                buttons: [
                    @if (session('role_id') == 1 || in_array(503, session('permissions')))

                        {
                            text: '<i class="la la-plus"></i> Add Fleet',
                            className: 'btn btn-primary add_fleet',
                            enabled: true,
                            action: function (e, dt, node, config) {
                                $('#AddFleetModal').modal('show');

                            }
                        },
                    @endif    
                    {
                        extend: 'excel',
                        title: 'Fleet Management',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                        className: 'btn btn-primary',
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                autoWidth: false,
                ajax: '{{ route('admin.settings.fleet.list') }}',
                rowId: 'id',
                order: [[0, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle text-center serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'reg_number', name: 'reg_number', class: 'align-middle text-center reg_number'},
                    {data: 'vehicle_type', name: 'vt.name', class: 'align-middle text-center vehicle_type'},
                    {data: 'tracking_id', name: 'tracking_id', class: 'align-middle text-center tracking_id'},
                    {data: 'status', name: 'status', class: 'align-middle text-center status'},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}

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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="1">Enabled</option>' +
                        '<option value="0">Disabled</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number')|| $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.status')){
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
            $('body').on('click','button.edit_fleet',function () {
            $('#editFleet').modal('show');
               var id = $(this).parents('tr').attr('id');
           
                $.get( "/admin/settings/fleet/"+id+"/edit/form", function( data ) {
                    $("#editFleetDiv").html(data);
                });
       });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {

                var id = table.row( $(this).parents('tr') ).data().id;

                console.log(id);

                if ($(this).hasClass('status')) {
                    $.ajax({
                        url: '{!! route('admin.settings.fleet.enable_disable') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function(data) {
                        console.log(data);
                        if (data.status) {
                            table.draw(true);
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });

                        }
                    });
                }
            });


            
            $('#fleet_add_form').validate({
                ignore: ":not(:visible),:disabled",
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Creating Fleet!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
                
            });

            
        });

    </script>
@endsection