@extends('admin.layout.master')

@section('title', 'Mapping (Cargo Manifest)')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Mapping (Cargo Manifest)
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;width: 100%">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Origin Hub</th>
                                    <th class="border-primary border-darken-1">Destination Hub</th>
                                    <th class="border-primary border-darken-1">Junctions</th>
                                    <th class="border-primary border-darken-1">Status</th>
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
                <form class="form-horizontal" method="POST" action="{{ route('admin.cargo.mapping.manifest.store') }}" novalidate="novalidate">
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
                        <div class="mt-1" id="add_junctions_container">
                            <div class="junction_container row">
                                <div class="col">
                                    <div class="form-group">
                                        <select name="junctions[1]" id="junction_1" class="select2 junctions" data-msg-required="Junction is Required">
                                            @foreach($cities as $junction)
                                                <option value="{{$junction->id}}">{{$junction->name}}</option>
                                            @endforeach
                                        </select>
                                        <span id="junction_error" class="text-danger small d-none">Junction 1 is required</span>
                                    </div>
                                </div>
                                <div class="col">
                                <div class="form-group">
                                    <button type="button" id="add_junction" class="btn btn-primary">Add Junction</button>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered" id="junction_table">
                                    <thead>
                                        <tr role="row" class="bg-primary white">
                                            <th class="border-primary border-darken-1">S.No</th>
                                            <th class="border-primary border-darken-1">Starting</th>
                                            <th class="border-primary border-darken-1">Ending</th>
                                            <th class="border-primary border-darken-1">Vehicle</th>
                                        </tr>
                                    </thead>
                                </table>
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
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.cargo.mapping.manifest.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Origin Hub');
                            head.push('Destination Hub');
                            head.push('Junctions');
                            head.push('Status');
                            head.push('Updated Date Time');
                            head.push('Updated By');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.junctions);
                                row.push(values.status);
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
                scrollX: true, scrollY: '500px',
                buttons: [
                @if (session('role_id') == 1 || in_array(548, session('permissions')))
                    {
                        text: '<i class="la la-plus-circle"></i> Add',
                        className: 'btn btn-primary add',
                        action: function (e, dt, node, config) {
                            $('#add_mapping').modal('show');
                        }
                    },
                @endif
                    {
                        extend: 'excel',
                        title: 'Cargo Manifest Mapping',
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
                    url: '{{ route('admin.cargo.mapping.manifest.list') }}'
                },
                rowId: 'id',
                order: [5, 'desc'],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'origin_display',  class: 'align-middle origin' ,orderable: false, searchable: false},
                    {data: 'destination_display',  class: 'align-middle destination',orderable: false, searchable: false},
                    {data: 'junctions_display',  class: 'align-middle junctions',orderable: false, searchable: false},
                    {data: 'status', name:"status",  class: 'align-middle status'},
                    {data: 'updated_at', name: 'updated_at', class: 'align-middle updated_at'},
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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Disabled</option>' +
                        '<option value="1">Enabled</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.origin') || $(header).is('.destination') || $(header).is('.junctions')) {
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


            var junction_table = $("#add_mapping form #junction_table").DataTable({
                dom: 'ltipr',
                paging:false,
                autoWidth: false,
                columns: [
                    {orderable: false, searchable: false, name: 'piece_serial_number', class: 'align-middle serial_number'},
                    {name: 'starting', class: 'align-middle starting', orderable: false, searchable: false},
                    {name: 'ending', class: 'align-middle ending', orderable: false, searchable: false},
                    {name: 'vehicle', class: 'align-middle vehicle', sortable: false, orderable: false, searchable: false}
                ],
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#add_mapping form .origin').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Origin Hub*'
            }).bind('change', function() {
                $(this).valid();
                junctions_display()
            });

            $('#add_mapping form .destination').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Destination Hub*'
            }).bind('change', function() {
                $(this).valid();
                junctions_display()
            });

            $('#add_mapping form .junctions').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Junction*',
                allowClear: true
            }).bind('change', function() {
                $(this).valid();
                junctions_display()
            });

            $('#add_mapping form #add_junction').on('click',function (){
                count = parseInt($(".junction_container").last().find('select').attr('id').replace("junction_","")) + 1;
                html = `
                <div class="junction_container row">
                    <div class="col">
                        <div class="form-group">
                            <select name="junctions[${count}]" class="select2 junctions" id="junction_${count}" data-rule-required="true" data-msg-required="Junction is Required">
                                @foreach($cities as $junction)
                <option value="{{$junction->id}}">{{$junction->name}}</option>
                                @endforeach
                </select>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <button type="button" class="btn btn-danger remove_junction">Remove Junction</button>
            </div>
        </div>
    </div>`;
                $("#add_junctions_container").append(html);

                $('#add_junctions_container #junction_'+count+'').prepend('<option value="" selected="selected"></option>').select2({
                    width: '100%',
                    placeholder: 'Junction*'
                }).bind('change', function() {
                    $(this).valid();
                    junctions_display();
                });
            });

            $(document).on('click',"#add_mapping form .remove_junction",function (){
                $(this).closest('.junction_container').remove();
                junctions_display()
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
                    if($(".junction_container .junctions").length > 1)
                    {
                        if($("#junction_1").val() == '')
                        {
                            $("#junction_error").removeClass('d-none');
                            return;
                        }
                    }
                    $("#junction_error").addClass('d-none');
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

            function make_vehicle_select(index)
            {
                vehicles = `<div class="form-group">
                                <select multiple="multiple" name="vehicles[${index}][]" id="vehicles_${index}" class="vehicles_select" data-msg-required="Vehicle is Required" data-rule-required="true">
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{$vehicle->id}}">{{$vehicle->reg_number}}</option>
                                    @endforeach
                                </select>
                             </div>`;

                return vehicles;
            }

            function make_junction_input(text,id,index)
            {
                var junction_html = `${text}<input type='hidden' value='${id}' name='route_junctions[${index}]'>`;

                return junction_html;
            }

            function junctions_display()
            {
                origin = $("#add_mapping form .origin").find(":selected").text();
                destination = $("#add_mapping form .destination").find(":selected").text();
                destination_id = $("#add_mapping form .destination").find(":selected").val();
                junction_table.rows().remove();

                if($("#add_mapping form .junction_container .junctions").length > 1)
                {
                    var lastIndex = $("#add_mapping form .junction_container .junctions").length - 1;
                    $("#add_mapping form .junction_container .junctions").each(function (index){
                        if(index == 0)
                        {
                            junction_table.row.add([index+1,origin,make_junction_input($(this).find(":selected").text(),$(this).find(":selected").val(),index+1),make_vehicle_select(index + 1)]);
                            previous_junction = $(this).find(":selected").text();
                        }
                        else{
                            junction_table.row.add([index+1,previous_junction,make_junction_input($(this).find(":selected").text(),$(this).find(":selected").val(),index+1),make_vehicle_select(index + 1)]);
                            previous_junction = $(this).find(":selected").text();
                        }
                    });
                    junction_table.row.add([lastIndex + 2,previous_junction,make_junction_input(destination,destination_id,lastIndex+2),make_vehicle_select(lastIndex + 2)]);
                }
                else{
                    if($("#add_mapping form .junction_container .junctions").first().val() != '')
                    {
                        junction_table.row.add([1,origin,make_junction_input($("#add_mapping form .junction_container .junctions").first().find(":selected").text(),$("#add_mapping form .junction_container .junctions").first().find(":selected").val(),1),make_vehicle_select(1)]);
                        junction_table.row.add([2,$("#add_mapping form .junction_container .junctions").first().find(":selected").text(),make_junction_input(destination,destination_id,2),make_vehicle_select(2)]);
                    }
                    else{
                        junction_table.row.add([1,origin,make_junction_input(destination,destination_id,1),make_vehicle_select(1)]);
                    }
                }
                junction_table.draw(false);
                junction_table.columns.adjust().draw();

                $("#add_mapping form .vehicles_select").select2({
                    width: '100%',
                    placeholder: 'Vehicle Numbers*'
                }).bind('select2:select', function(e){
                    var current_val = e.params.data.id;
                    var select = $(this);
                    $("#add_mapping form .vehicles_select").each(function (){
                        if($.inArray(current_val,$(this).val()) != -1 && $(this).attr('id') != select.attr('id'))
                        {
                            toastr.error("Vehicle Can Not Be Repeated", 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                            var arr = $(select).val();
                            arr.splice(arr.indexOf(current_val),1);
                            $(select).val(arr).trigger('change');
                        }
                    });
                });
            }

            $('body').on('click','.status_mapping',function () {
                var mapping_id = $(this).parents('tr').attr('id');
                swal({
                    text: 'Are you sure, you want to update mapping status',
                    icon: 'info',
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
                }).then(function(confirm) {
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Status is being updated!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        $.ajax({
                            url:'{!! route("admin.cargo.mapping.manifest.status") !!}',
                            method: 'POST',
                            data: {
                                'id': mapping_id,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status == 0)
                            {
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            else{
                                toastr.success(data.success, 'Success!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                            }
                            swal.close();
                            table.draw();
                        });
                    }
                });
            });
        });
    </script>
@endsection