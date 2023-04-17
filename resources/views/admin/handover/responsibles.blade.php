@extends('admin.layout.master')

@section('title', 'Responsibles')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                   Responsibles
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="width:100%;z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Name</th>
                                    <th class="border-primary border-darken-1">Hub</th>
                                    <th class="border-primary border-darken-1">Area</th>
                                    <th class="border-primary border-darken-1">Created by</th>
                                    <th class="border-primary border-darken-1">Updated by</th>
                                    <th class="border-primary border-darken-1">Status</th>
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

    <div class="modal fade text-left" id="AddResponsibleModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddTierModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Responsible</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body text-center">
                    <form id="add_responsible_form" action="{{route('admin.handover.responsibles.add')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-6 form-group">
                                    <input  class="form-control" id="name" name="name" type="text" placeholder="Enter Name"
                                    data-rule-required="true" data-msg-required="" />
                                </div>
                            </div>
                            <br>
                            <div class="row justify-content-center">
                               <div class="col-6 form-group">
                                    <fieldset class="form-group">
                                            <select name="hub" id="hub" class="form-control select2" data-rule-required="true" data-msg-required="">
                                                @foreach($hubs as $hub)
                                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                                @endforeach
                                            </select>
                                    </fieldset>
                               </div>
                            </div>
                            <br>
                            <div class="row justify-content-center">
                                <div class="col-6">
                                        <button id="AddnewTier" type="submit" class="btn btn-primary btn-block">Add Responsible</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade text-left" id="EditResponsibleModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditTierModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit Responsible</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="edit_responsible_form" action="{{route('admin.handover.responsibles.edit')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <input type="hidden" id="responsible_id" name="id">
                                    <input  class="form-control" id="edit_name" name="name" type="text" placeholder="Enter Tier Name"
                                        data-rule-required="true" data-msg-required="" />
                                </div>
                            </div>
                            <br>
                            <div class="row justify-content-center">
                               <div class="col-6">
                                    <fieldset class="form-group">
                                        <select name="hub" id="edit_hub" class="form-control select2" data-rule-required="true" data-msg-required="">
                                            @foreach($hubs as $hub)
                                                <option value="{{$hub->id}}">{{$hub->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            <br><br>
                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <button id="AddnewTier" type="submit" class="btn btn-primary btn-block">Edit Responsible</button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
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
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#hub').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder: 'Select Hub',
            dropdownParent:$('#add_responsible_form')
        });

        $( "#add_responsible_form" ).validate({
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {
                $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Responsible is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
            }
        });


        $( "#edit_responsible_form" ).validate({
            errorClass:"danger",
            errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Responsible is being Edited!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
        });


        $('body').on('click','button.edit',function () {
            var id = $(this).parents('tr').attr('id');
            // var hub= $(this).parents('tr').attr('hub');
            var hub = $(this).parents('tr').attr('hub'); 
            // var hub = $(this).find(':selected');
            // var hub_id =  $('#edit_hub :selected').val();

            // var hub = $(this).find(':selected');
            //     var hub_id = hub.val();
            //var status = table.row($(this).parents('tr')).data().status;
            

            $.ajax({
                    url: '{!! route('admin.handover.responsibles.details') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        $('#responsible_id').val(data.responsible.id);
                        $('#edit_name').val(data.responsible.name);
                       
                        $("#edit_hub").select2({
                            width:'100%',
                            class:'form-control',
                            dropdownParent:$('#edit_responsible_form')
                        });

                        $('#edit_hub').val(hub).trigger('change');
                
                        $('#EditResponsibleModal').modal('show');

                    }
                    else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
            });
        });


        var table = $('#datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            buttons: [{
                text: '<i class="la la-cogs"></i> Add',
                className: 'btn btn-primary add',
                action: function (e, dt, node, config) {
                $('#AddResponsibleModal').modal('show');
                }
            }, 
            {
                extend: 'excel',
                title: 'Responsibles Sheet',
                className: 'btn btn-primary',
                text: '<i class="la la-file-excel-o"></i> Excel',
            },'reset'],
            scrollX: true, scrollY: '500px',
            lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
            pageLength: 50,
            pagingType: 'full_numbers',
            processing: true,
            language: {
                processing: data_table_loader
            },
            serverSide: true,
            ajax: '{{ route('admin.handover.responsibles.list') }}',
            rowId: 'responsible_id',
            order: [[1, 'asc']],
            columns: [
                {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                {data: 'name', name: 'handover_responsibilities.name', class: 'align-middle name'},
                {data: 'hub', name: 'c.name', class: 'align-middle hub'},
                {data: 'area', name: 'ca.name', class: 'align-middle area'},
                {data: 'created', name: 'a.name', class: 'align-middle created'},
                {data: 'updated', name: 'u.name', class: 'align-middle updated'},
                {data: 'status', name: 'handover_responsibilities.status', class: 'align-middle status'},
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
                    '<option value="0">Disable</option>' +
                    '<option value="1">Enable</option>' +
                    '</select>';
                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();

                    if ($(header).is('.serial_number') || $(header).is('.action')) {
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
                $("#status_select").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select Status",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                this.api().table().columns.adjust();
            }
        });


        $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
            var id = parseInt($(this).parents('tr').attr('id'));
            if ($(this).hasClass('enable')) {
                $.ajax({
                    url: '{!! route('admin.handover.responsibles.status') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        'status': 1,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        if (data.status == 0) {
                            table.draw(false);

                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }
                        else {
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
            }
            else if ($(this).hasClass('disable')) {
                $.ajax({
                    url: '{!! route('admin.handover.responsibles.status') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        'status': 0,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        if (data.status == 0) {
                            table.draw(false);

                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }
                        else {
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
            }

        });
        $('#EditResponsibleModal').on('hidden.bs.modal', function() {
            $('#responsible_id').val('');
            $('#edit_name').val('');
        });
    });


    </script>
@endsection