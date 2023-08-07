@extends('admin.layout.master')
@section('title','Rider Assigned Hub')

@section('content')
    <h1 class="mb-1">
        Rider Assigned Hub
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Rider Name</th>
                        <th class="border-primary border-darken-1">Hub(s)</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="add_rider_hub_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="add_rider_hub_modal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Assign Hub</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="settings_form" class="form-horizontal" action="{{ route('admin.settings.rider_assigned_hub.add') }}" method="POST" novalidate="novalidate">
                    @csrf
                    @method("POST")
{{--                    <input type="hidden" name="shipper_id" id="shipper_id">--}}
                    <div class="modal-body">
                        <div class="row justify-content-center">
                            <div class="col-10 form-group">
                                <select class="form-control" name="select_rider_id" id="select_rider_id" data-rule-required="true" data-msg-required="Rider is required">
                                    @foreach($riders as $select_rider)
                                        <option value="{{$select_rider->id}}">{{$select_rider->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-10 form-group">
                                <select name="hubs[]" id="hub_select" class="form-control select2" multiple="multiple" data-msg-required="Atleast one Hub is required" data-rule-required="true" required="required">
                                    @foreach($hubs as $hub)
                                        <option value="{{$hub->id}}">{{$hub->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--edit--}}
    <div class="modal fade text-left" id="edit_rider_hub_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="edit_rider_hub_modal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Assign Hub</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="settings_form" class="form-horizontal" action="{{ route('admin.settings.rider_assigned_hub.edit') }}" method="POST" novalidate="novalidate">
                    @csrf
                    @method("POST")
{{--                    <input type="hidden" name="shipper_id" id="shipper_id">--}}
                    <div class="modal-body">
                        <div class="row justify-content-center">
                            <div class="col-10 form-group">
                                <select class="form-control" name="select_rider_id" id="select_rider_id" data-rule-required="true" data-msg-required="Rider is required">
                                    @foreach($riders as $select_rider)
                                        <option value="{{$select_rider->id}}">{{$select_rider->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-10 form-group">
                                <select name="hubs[]" id="hub_select" class="form-control select2" multiple="multiple" data-msg-required="Atleast one Hub is required" data-rule-required="true" required="required">
                                    @foreach($riders as $hub)
                                        <option value="{{$hub->id}}">{{$hub->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info">Update</button>
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
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $("#select_rider_id").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Rider",
                width:'300px',
                dropdownParent:$('#add_rider_hub_modal')
            });

            $('#hub_select').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            }).bind('select2:select', function () {

                if($(this).val().length != 0){
                    $('#settings_form').find('button[type=submit]').prop('disabled', false);
                }
            });

            $('#hub_select').on('select2:unselect', function () {
                if($(this).val().length == 0){
                    $('#settings_form').find('button[type=submit]').prop('disabled', true);
                }
            });

            var table = $('#datatable').DataTable({

                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    buttons:[{
                        className: 'btn btn-primary mb-1',
                        text: '<i class="la la-plus"></i> Add Rider',
                        action:function (e) {
                            $('#add_rider_hub_modal').modal('show');
                        }
                    },'reset'],

                    lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                    pageLength: 50,
                    pagingType: 'full_numbers',
                    processing: true,
                    language: {
                        processing: data_table_loader
                    },
                    serverSide: true,
                    ajax: '{{ route('admin.settings.rider_assigned_hub.list') }}',
                    rowId: 'id',
                    order: [1, 'desc'],
                    columns: [
                        {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                        {data: 'name', name: 'name', class: 'align-middle name'},
                        {data: 'hubs', name: 'hubs', class: 'align-middle name'},
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

                        this.api().columns().every(function(column_id) {
                            var column = this;
                            var header = column.header();

                            if ($(header).is('.action') || $(header).is('.serial_number')) {
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

            $('body').on('click','#datatable button.edit',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id){
                    $.ajax({
                        url: '{!! route('admin.settings.return.reason.get') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'reason_id': id,
                        }
                    }).done(function(data){
                        if(data.status == 0){
                            $('#edit_reason_id').val(id);
                            $('#edit_reason').val(data.reason);
                            $('#edit_rider_hub_modal').modal('show');
                        }
                    });
                }
            });

            $('body').on('change','#add_rider_hub_modal #reason,#edit_rider_hub_modal #edit_reason',function() {
                $(this).val($(this).val().trim());
            });

            $('body').on('hidden.bs.modal','#add_rider_hub_modal',function () {
                $('#reason').val('');
            });
            $('body').on('hidden.bs.modal','#edit_rider_hub_modal',function () {
                $('#edit_reason').val('');
            });

            $('body').on('click','#editReason', function () {
                var reason = $('#edit_reason').val();
                var reason_id = parseInt($('#edit_reason_id').val());

                if(reason != '' && reason_id != ''){
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to update the reason!',
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
                                url: '{!! route('admin.settings.return.reason.edit') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'reason_id': reason_id,
                                    'reason':reason
                                }
                            }).done(function(data){
                                if(data.status == 0){
                                    table.draw(true);
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                    $('#edit_rider_hub_modal').modal('hide');
                                }
                            });
                        }
                    });

                }else{
                    var error = 'Reason required!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });

            $('#settings_form').validate({
                // ignore: ":not(:visible),:disabled",
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to update Shippers.',
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
                        if(confirm){
                            $(form).find('button[type=submit]').attr('disabled', 'disabled');
                            blockPagePermanently();
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
@endsection