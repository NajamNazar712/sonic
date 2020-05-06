@extends('admin.layout.master')
@section('title','Return Reasons')

@section('content')
    <h1 class="mb-1">
        Return Reasons
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Reason</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AddReasonModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddReasonModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Return Reason</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  text-center">
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <input name="reason" id="reason" class="form-control reason" placeholder="Enter Reason">
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <button id="addReason" type="button" class="btn btn-primary btn-block">Add</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{--edit--}}
    <div class="modal fade text-left" id="EditReasonModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditReasonModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit Reason</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" id="edit_reason_id">
                <div class="modal-body  text-center">
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <input name="edit_reason" id="edit_reason" class="form-control edit_reason" placeholder="Enter Head of Account">
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <button id="editReason" type="button" class="btn btn-primary btn-block">Edit</button>
                        </div>
                    </div>

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
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#datatable').DataTable({

                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[{
                    className: 'btn btn-primary mb-1',
                    text: '<i class="la la-plus"></i> Add Reason',
                    action:function (e) {
                        $('#AddReasonModal').modal('show');
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
                ajax: '{{ route('admin.settings.return.reason.list') }}',
                rowId: 'id',
                order: [1, 'desc'],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'name', class: 'align-middle name'},
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
                            $('#EditReasonModal').modal('show');
                        }
                    });
                }
            });
            $('body').on('change','#AddReasonModal #reason,#EditReasonModal #edit_reason',function() {
                $(this).val($(this).val().trim());
            });

            $('body').on('click','#addReason', function () {
                var reason = $('#reason').val();
                if(reason != ''){

                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to add the reason!',
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
                                url: '{!! route('admin.settings.return.reason.add') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'reason': reason
                                }
                            }).done(function(data){
                                if(data.status == 0){
                                    table.draw(true);
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    $('#AddReasonModal').modal('hide');
                                }else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            });
                        }
                    });

                }else{
                    var error = 'Please enter reason!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }


            });
            $('body').on('hidden.bs.modal','#AddReasonModal',function () {
                $('#reason').val('');
            });
            $('body').on('hidden.bs.modal','#EditReasonModal',function () {
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

                                    $('#EditReasonModal').modal('hide');
                                }
                            });
                        }
                    });

                }else{
                    var error = 'Reason required!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });


        });
    </script>
@endsection