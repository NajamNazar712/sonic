@extends('admin.layout.master')
@section('title','Petty Cash Heads')

@section('content')
    <h1 class="mb-1">
        Petty Cash Heads
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Heads</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1"></th>

                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AccountHeadModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AccountHeadModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Head Of Account</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  text-center">
                        <div class="row mb-2 justify-content-center">
                            <div class="col-12 form-group">
                                <input name="account_head" id="account_head" class="form-control account_head" placeholder="Enter Head of Account">
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <button id="addHead" type="button" class="btn btn-primary btn-block">Add</button>
                            </div>
                        </div>

                </div>
            </div>
        </div>
    </div>

    {{--edit--}}
    <div class="modal fade text-left" id="EditAccountHeadModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditAccountHeadModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit Head Of Account</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" id="edit_account_head_id">
                <div class="modal-body  text-center">
                        <div class="row mb-2 justify-content-center">
                            <div class="col-12 form-group">
                                <input name="edit_account_head" id="edit_account_head" class="form-control edit_account_head" placeholder="Enter Head of Account">
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <button id="editHead" type="button" class="btn btn-primary btn-block">Edit</button>
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

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                @if (session('role_id') == 1 || in_array(159, session('permissions')))
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[{
                    title: 'Add',
                    className: 'btn btn-primary mb-1',
                    text: '<i class="la la-plus"></i> Add Head',
                    action:function (e) {
                        $('#AccountHeadModal').modal('show');
                    }
                }],
                @else
                dom: 'ltipr',
                @endif
                paging:false,
                bInfo:false,
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.settings.petty_cash.heads.list') }}',
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

                    this.api().table().columns.adjust();
                }
            });
            $('body').on('click','#datatable button.edit',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                var account_head = $(this).parents('tr').find('td.name').text();
                if(id){
                    $('#edit_account_head_id').val(id);
                    $('#EditAccountHeadModal').modal('show');
                    $('#edit_account_head').val(account_head);
                }else{
                    var error = 'Head ID Not Found, Please Try again!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });
            $('body').on('change','#AccountHeadModal #account_head,#EditAccountHeadModal #edit_account_head',function() {
                $(this).val($(this).val().trim());
            });
            $('body').on('click','#addHead', function () {
               var head = $('#account_head').val();
               if(head != ''){
                   $.ajax({
                       url: '{!! route('admin.settings.petty_cash.heads.add') !!}',
                       method: 'POST',
                       data: {
                           '_token': '{{ csrf_token() }}',
                           'head': head
                       }
                   }).done(function(data){
                       if(data.status){
                           table.draw(true);
                           toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        $('#account_head').val('');
                        $('#AccountHeadModal').modal('hide');
                       }else{
                           toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                       }
                   });

               }else{
                   var error = 'Head of Account is empty!';
                   toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
               }
            });
            $('body').on('click','#editHead', function () {
               var head = $('#edit_account_head').val();
               var id = parseInt($('#edit_account_head_id').val());
                if(head != '' && id != ''){
                   $.ajax({
                       url: '{!! route('admin.settings.petty_cash.heads.edit') !!}',
                       method: 'POST',
                       data: {
                           '_token': '{{ csrf_token() }}',
                           'head_id': id,
                           'account_head':head
                       }
                   }).done(function(data){
                       if(data.status){
                           table.draw(true);
                           toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                           $('#edit_account_head').val('');
                           $('#EditAccountHeadModal').modal('hide');
                       }
                   });

               }else{
                   var error = 'Head of Account is empty!';
                   toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
               }
            });

            $('body').on('click','#datatable button.enable', function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id){
                    $.ajax({
                        url: '{!! route('admin.settings.petty_cash.heads.active') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'head_id': id
                        }
                    }).done(function(data){
                        if(data.status){
                            table.draw(true);
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }
                    });

                }else{
                    var error = 'Head of Account ID Not Found!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });

            $('body').on('click','#datatable button.inactive', function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id){
                    $.ajax({
                        url: '{!! route('admin.settings.petty_cash.heads.inactive') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'head_id': id
                        }
                    }).done(function(data){
                        if(data.status){
                            table.draw(true);
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }
                    });

                }else{
                    var error = 'Head of Account ID Not Found!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });
        });
    </script>
@endsection