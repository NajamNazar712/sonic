@extends('admin.layout.master')
@section('title','Month Closing Types')

@section('content')
    <h1 class="mb-1">
        Month Closing Types
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Type</th>
                        <th class="border-primary border-darken-1"></th>

                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="TypeModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="TypeModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Closing Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  text-center">
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <input name="type_name" id="type_name" class="form-control type_name" placeholder="Enter closing type name">
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <button id="addType" type="button" class="btn btn-primary btn-block">Add</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{--edit--}}
    <div class="modal fade text-left" id="EditTypeModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditTypeModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit Closing Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" id="edit_closing_type_id">
                <div class="modal-body  text-center">
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <input name="edit_closing_type" id="edit_closing_type" class="form-control edit_closing_type" placeholder="Enter closing type">
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <button id="edit_type" type="button" class="btn btn-primary btn-block">Edit</button>
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

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[{
                    title: 'Add',
                    className: 'btn btn-primary',
                    text: '<i class="la la-plus"></i> Add Types',
                    action:function (e) {
                        $('#TypeModal').modal('show');
                    }
                },'reset'],
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                scrollY:'300px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                ajax: '{{ route('admin.settings.month_closing.types.list') }}',
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
                var type_name = $(this).parents('tr').find('td.name').text();
                if(id){
                    $('#edit_closing_type_id').val(id);
                    $('#EditTypeModal').modal('show');
                    $('#edit_closing_type').val(type_name);
                }else{
                    var error = 'Type ID Not Found, Please Try again!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });
            $('body').on('change','#TypeModal #type_name,#EditTypeModal #edit_closing_type',function() {
                $(this).val($(this).val().trim());
            });
            $('body').on('click','#addType', function () {
                var type = $('#type_name').val();
                if(type != ''){
                    $.ajax({
                        url: '{!! route('admin.settings.month_closing.types.add') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'type': type
                        }
                    }).done(function(data){
                        if(data.status){
                            table.draw(true);
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            $('#type_name').val('');
                            $('#TypeModal').modal('hide');
                        }else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });

                }else{
                    var error = 'Month Closing Type is empty!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });
            $('body').on('click','#edit_type', function () {
                var type = $('#edit_closing_type').val();
                var id = parseInt($('#edit_closing_type_id').val());
                if(type != '' && id != ''){
                    $.ajax({
                        url: '{!! route('admin.settings.month_closing.types.edit') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'type_id': id,
                            'type_name':type
                        }
                    }).done(function(data){
                        if(data.status){
                            table.draw(true);
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            $('#edit_closing_type').val('');
                            $('#EditTypeModal').modal('hide');
                        }
                    });

                }else{
                    var error = 'Month Closing Type is empty!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });

        });
    </script>
@endsection