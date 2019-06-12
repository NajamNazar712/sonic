@extends('admin.layout.master')

@section('title', 'Mapping')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Mapping
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            {{--<form id="shipment_type_search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">--}}
                                {{--<div class="form-group">--}}
                                    {{--<select name="shipment_type" class="select2" id="shipment_type">--}}
                                        {{--<option value="" selected="selected"></option>--}}
                                        {{--<option value="0">All</option>--}}
                                        {{--<option value="1">Normal</option>--}}
                                        {{--<option value="2">Return</option>--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</form>--}}

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Origin Hub</th>
                                    <th class="border-primary border-darken-1">Destination Hub</th>
                                    <th class="border-primary border-darken-1">Junction 1</th>
                                    <th class="border-primary border-darken-1">Junction 2</th>
                                    <th class="border-primary border-darken-1">Receiver</th>
                                    <th class="border-primary border-darken-1">Updated Date</th>
                                    <th class="border-primary border-darken-1">Updated By</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_mapping" role="dialog" aria-labelledby="add_mapping_title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form class="form-horizontal" method="POST" action="{{ route('admin.cargo.mapping.store') }}" novalidate="novalidate">
                    {{ csrf_field() }}

                    <div class="modal-header">
                        <h4 class="modal-title" id="add_mapping_title">Add Mapping</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <select name="origin" class="select2 origin" data-rule-required="true" data-msg-required="Origin Hub is required">
                                            @foreach($cities as $city)
                                                <option value="{{$city->id}}">{{$city->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-group">
                                        <select name="destination" class="select2 destination" data-rule-required="true" data-msg-required="Destination Hub is required">
                                            @foreach($cities as $city)
                                                <option value="{{$city->id}}">{{$city->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                        </div>
                        <div class="row mt-1">
                                <div class="col">
                                    <div class="form-group">
                                        <select name="junction_1" class="select2 junction_1" data-rule-required="true" data-msg-required="Junction 1 is required">
                                            @foreach($junctions as $junction)
                                                <option value="{{$junction->id}}">{{$junction->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-group">
                                        <select name="junction_2" class="select2 junction_2">
                                            @foreach($junctions as $junction)
                                                <option value="{{$junction->id}}">{{$junction->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            <div class="col">
                                <div class="form-group">
                                    <select name="receiver_id" class="select2 receiver_id">
                                        @foreach($admins as $admin)
                                            <option value="{{$admin->id}}">{{$admin->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-center justify-content-around">
                        <button type="submit" name="submit" class="btn btn-primary" value="submit">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_mapping" role="dialog" aria-labelledby="edit_mapping_title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form class="form-horizontal" method="POST" action="{{ route('admin.cargo.mapping.update') }}" novalidate="novalidate">
                    {{ csrf_field() }}

                    <input type="hidden" class="mapping_id" name="mapping_id" value="">

                    <div class="modal-header">
                        <h4 class="modal-title" id="add_mapping_title">Edit Mapping</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <input type="hidden" name="origin" class="origin">

                                    <p class="mt-1 border-bottom border-light text-center font-medium-1 text-bold-600 origin_line"></p>
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <input type="hidden" name="destination" class="destination">

                                    <p class="mt-1 border-bottom border-light text-center font-medium-1 text-bold-600 destination_line"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col">
                                <div class="form-group">
                                    <label>Junction 1:<span class="red">*</span></label>
                                    <select name="junction_1" class="select2 junction_1" data-rule-required="true" data-msg-required="Junction 1 is required">
                                        @foreach($junctions as $junction)
                                            <option value="{{$junction->id}}">{{$junction->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label>Junction 2:</label>
                                    <select name="junction_2" class="select2 junction_2">
                                        @foreach($junctions as $junction)
                                            <option value="{{$junction->id}}">{{$junction->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label>Receiver Name:</label>
                                    <select name="receiver_id" class="select2 receiver_id">
                                        @foreach($admins as $admin)
                                            <option value="{{$admin->id}}">{{$admin->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-center justify-content-around">
                        <button type="submit" name="submit" class="btn btn-primary" value="submit">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.cargo.mapping.list') }}',
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Origin Hub');
                            head.push('Destination Hub');
                            head.push('Junction 1');
                            head.push('Junction 2');
                            head.push('Receiver');
                            head.push('Updated Date Time');
                            head.push('Updated By');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.junction_1);
                                row.push(values.junction_2);
                                row.push(values.receiver);
                                row.push(values.updated_at);
                                row.push(values.updated_by);


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
                        text: '<i class="la la-plus-circle"></i> Add',
                        className: 'btn btn-primary add',
                        action: function (e, dt, node, config) {
                            $('#add_mapping').modal('show')
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Cargo Mapping',
                        className:'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
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
                ajax: {
                    url: '{{ route('admin.cargo.mapping.list') }}'
                },
                rowId: 'id',
                order: [6, 'desc'],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'pickup_notes.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'junction_1', name: 'jc1.name', class: 'align-middle junction_1'},
                    {data: 'junction_2', name: 'jc2.name', class: 'align-middle junction_2'},
                    {data: 'receiver', name: 'ar.name', class: 'align-middle receiver'},
                    {data: 'updated_at', name: 'junction_mappings.updated_at', class: 'align-middle updated_at'},
                    {data: 'updated_by', name: 'a.name', class: 'align-middle updated_by'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
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
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '</select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control">' +
                        '</select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control">' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
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

            $('#add_mapping form .origin').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Origin Hub*'
            }).bind('change', function() {
                $(this).valid();
            });

            $('#add_mapping form .destination').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Destination Hub*'
            }).bind('change', function() {
                $(this).valid();
            });

            $('#add_mapping form .junction_1').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Junction 1*'
            }).bind('change', function() {
                $(this).valid();
            });

            $('#add_mapping form .junction_2').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Junction 2',
                allowClear: true
            });

            $('#add_mapping form .receiver_id').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Receiver Name',
                allowClear: true
            }).bind('change', function() {
                $(this).valid();
            });

            $('#add_mapping form').validate({
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
                    blockPagePermanently();
                    swal({
                        title: 'Please Wait!',
                        text: 'Mapping is being Added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });

            $('body').on('click','.edit_mapping',function () {
                var mapping_id = $(this).parents('tr').attr('id');
                $.ajax({
                    url:'{!! route("admin.cargo.mapping.edit") !!}',
                    method: 'POST',
                    data: {
                        'mapping_id': mapping_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    $('#edit_mapping').modal('show');
                    $('#edit_mapping form .mapping_id').val(mapping_id);
                    $('#edit_mapping form .origin').val(data.details['origin_id']);
                    $('#edit_mapping form .origin_line').html(data.origin['name']);
                    $('#edit_mapping form .destination').val(data.details['destination_id']);
                    $('#edit_mapping form .destination_line').html(data.destination['name']);
                    $('#edit_mapping form .junction_1').val(data.details['junction_1']);

                    $('#edit_mapping form .junction_1').select2({
                        width: '100%',
                        placeholder: 'Junction 1*'
                    }).bind('change', function() {
                        $(this).valid();
                    });
                    if(data.details['junction_2'] !== null) {
                        $('#edit_mapping form .junction_2').val(data.details['junction_2']);
                        $('#edit_mapping form .junction_2').select2({
                            width: '100%',
                            placeholder: 'Junction 2',
                            allowClear: true
                        });
                    }
                    else{
                        $('#edit_mapping form .junction_2').prepend('<option value="" selected="selected"></option>').select2({
                            width: '100%',
                            placeholder: 'Junction 2',
                            allowClear: true
                        }).bind('change', function() {
                            $(this).valid();
                        });
                    }
                    if(data.details['receiver'] !== null) {
                        $('#edit_mapping form .receiver_id').val(data.details['receiver']);
                        $('#edit_mapping form .receiver_id').select2({
                            width: '100%',
                            placeholder: 'Receiver Name',
                            allowClear: true
                        }).bind('change', function () {
                            $(this).valid();
                        });
                    }
                    else{
                        $('#edit_mapping form .receiver_id').prepend('<option value="" selected="selected"></option>').select2({
                            width: '100%',
                            placeholder: 'Receiver Name',
                            allowClear: true
                        }).bind('change', function() {
                            $(this).valid();
                        });
                    }
                });

                $('#edit_mapping form').validate({
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
                        blockPagePermanently();
                        swal({
                            title: 'Please Wait!',
                            text: 'Mapping is being Updated!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection