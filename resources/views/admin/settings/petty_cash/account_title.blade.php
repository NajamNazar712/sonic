@extends('admin.layout.master')
@section('title','Petty Cash Titles')

@section('content')
    <h1 class="mb-1">
        Petty Cash Titles
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Titles</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1"></th>

                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AccountTitleModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AccountTitleModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Title Of Account</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  text-center">
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <select name="heads[]" id="head_select" class="form-control select2" multiple="multiple">
                                @foreach($heads as $head)
                                    <option value="{{$head->id}}">{{$head->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 form-group">
                            <input name="account_title" id="account_title" class="form-control account_title" placeholder="Enter Title of Account">
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <button id="addTitle" type="button" class="btn btn-primary btn-block">Add</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{--edit--}}
    <div class="modal fade text-left" id="EditAccountTitleModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditAccountTitleModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit Title Of Account</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" id="edit_account_title_id">
                <div class="modal-body  text-center">
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <select name="edit_heads[]" id="edit_head_select" class="form-control select2" multiple="multiple">
                                @foreach($heads as $head)
                                    <option value="{{$head->id}}">{{$head->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 form-group">
                            <input name="edit_account_title" id="edit_account_title" class="form-control edit_account_title" placeholder="Enter Head of Account">
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <button id="editTitle" type="button" class="btn btn-primary btn-block">Edit</button>
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

    <script type="text/javascript">
        $(document).ready(function () {

            $('#head_select').select2({
                placeholder:'Account Head',
                width:'100%',
                allowClear:true
            });
            $('#edit_head_select').select2({
                placeholder:'Account Head',
                width:'100%',
                allowClear:true
            });
            $('#search_filter_btn').on('click',function () {
                table.draw();
            });
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                @if (session('role_id') == 1 || in_array(163, session('permissions')))
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[{
                    title: 'Add',
                    className: 'btn btn-primary mb-1',
                    text: '<i class="la la-plus"></i> Add Title',
                    action:function (e) {
                        $('#AccountTitleModal').modal('show');
                    }
                }],
                @else
                dom: 'ltipr',
                @endif
                scrollX: true, scrollY:'300px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.settings.petty_cash.titles.list') }}',
                rowId: 'id',
                order: [1, 'asc'],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'name', class: 'align-middle name'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
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
                        '<option value="0">Inactive</option>' +
                        '<option value="1">Active</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(drop_select).appendTo($(search))
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
                        placeholder: "Search Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
            $('body').on('click','#datatable button.edit',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                // var account_title = $(this).parents('tr').find('td.name').text();
                if(id){
                    $.ajax({
                        url: '{!! route('admin.settings.petty_cash.titles.info') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'title_id': id
                        }
                    }).done(function(data){

                        if(data.status){
                            $('#edit_account_title_id').val(id);
                            $('#EditAccountTitleModal').modal('show');
                            $('#edit_account_title').val(data.title.name);
                            $('#edit_head_select').val(data.heads).trigger('change');
                        }else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });


                }else{
                    var error = 'Head ID Not Found, Please Try again!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });
            $('body').on('change','#AccountTitleModal #account_title,#EditAccountTitleModal #edit_account_title',function() {
                $(this).val($(this).val().trim());
            });

            $('body').on('click','#addTitle', function () {
                var heads = $('#head_select').val();
                var title = $('#account_title').val();

            if(heads.length > 0){
                if(title != ''){

                    $.ajax({
                        url: '{!! route('admin.settings.petty_cash.titles.add') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'heads': heads,
                            'title': title
                        }
                    }).done(function(data){
                        if(data.status){
                            table.draw(true);
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                            $('#AccountTitleModal').modal('hide');
                        }else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });

                }else{
                    var error = 'Enter Title of Account!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }

            }else{
                    var error = 'Select atleast one Head of Account!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            }
            });
            $('body').on('hidden.bs.modal','#AccountTitleModal',function () {
               $('#account_title').val('');
               $('#head_select').val(null).trigger('change');
            });
            $('body').on('hidden.bs.modal','#EditAccountTitleModal',function () {
               $('#edit_account_title').val('');
               $('#edit_head_select').val(null).trigger('change');
               $('#edit_account_title_id').val('');
            });


            $('body').on('click','#editTitle', function () {
                var title = $('#edit_account_title').val();
                var id = parseInt($('#edit_account_title_id').val());
                var heads = $('#edit_head_select').val();
                if(title != '' && id != '' && heads.length > 0){
                    $.ajax({
                        url: '{!! route('admin.settings.petty_cash.titles.edit') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'title_id': id,
                            'account_title':title,
                            'heads' :heads
                        }
                    }).done(function(data){
                        if(data.status){
                            table.draw(true);
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                            $('#EditAccountTitleModal').modal('hide');
                        }
                    });

                }else{
                    var error = 'Fill all fields!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });

            $('body').on('click','#datatable button.enable', function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id){
                    $.ajax({
                        url: '{!! route('admin.settings.petty_cash.titles.active') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'title_id': id
                        }
                    }).done(function(data){
                        if(data.status){
                            table.draw(true);
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }
                    });

                }else{
                    var error = 'Title of Account ID Not Found!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });

            $('body').on('click','#datatable button.inactive', function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id){
                    $.ajax({
                        url: '{!! route('admin.settings.petty_cash.titles.inactive') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'title_id': id
                        }
                    }).done(function(data){
                        if(data.status){
                            table.draw(true);
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }
                    });

                }else{
                    var error = 'Title of Account ID Not Found!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });
        });
    </script>
@endsection