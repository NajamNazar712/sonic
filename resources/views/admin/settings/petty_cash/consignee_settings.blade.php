@extends('admin.layout.master')
@section('title','Petty Cash Hub Assigning')

@section('content')
    <h1 class="mb-1">
        Petty Cash Hub Assigning
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Consignee</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AddConsigneeModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddConsigneeModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Petty Cash Consignee</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  text-center">
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <input name="consignee" class="form-control" id="consignee" placeholder="Consignee Name*" data-rule-required="true" data-msg-required="Consignee Name is required">
                            <span class="text-danger font-small-3 myError" id="consignee_error"></span>
                        </div>
                    </div>
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <select name="hub" class="select2" id="hub" data-rule-required="true" data-msg-required="Hub is required">
                                @foreach($hubs as $hub)
                                    <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger font-small-3 myError" id="hub_error"></span>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <button id="addConsignee" type="button" class="btn btn-primary btn-block">Add</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{--edit--}}
    <div class="modal fade text-left" id="EditConsigneeModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditConsigneeModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit Petty Cash Consignee</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" id="edit_petty_cash_consignee_id">
                <div class="modal-body  text-center">
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <input name="consignee" class="form-control" id="edit_consignee" placeholder="Consignee Name*" data-rule-required="true" data-msg-required="Consignee Name is required">
                            <span class="text-danger font-small-3 myError" id="edit_consignee_error"></span>
                        </div>
                    </div>
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <select name="hub" class="select2" id="edit_hub" data-rule-required="true" data-msg-required="Hub is required">
                                @foreach($hubs as $hub)
                                    <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger font-small-3 myError" id="edit_hub_error"></span>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <button id="editConsignee" type="button" class="btn btn-primary btn-block">Edit</button>
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
                    title: 'Add',
                    className: 'btn btn-primary',
                    text: '<i class="la la-plus"></i> Add Consignee',
                    action:function (e) {
                        $(".myError").html('');
                        $('#consignee').val('');
                        $('#hub').val('');
                        $('#hub').trigger('change');
                        $('#AddConsigneeModal').modal('show');
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
                ajax: '{{ route('admin.settings.petty_cash.consignee.list') }}',
                rowId: 'id',
                order: [1, 'desc'],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'consignee_name', name: 'petty_cash_consignees.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'hub_name', name: 'hubs.name', class: 'align-middle hub_name'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

                ],
                createdRow: function( row, data, dataIndex ) {
                    $( row ).find('td:eq(2)').attr('data-hub_id', data.hub_id);
                },
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var hub_select = '<select name="hub_select" id="hub_select" class="select2 form-control"></select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.hub_name')){
                            $(hub_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                    });
                    var hub_data = $.map({!! $hubs !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });


                    $("#hub_select").prepend('<option value="" selected></option>').select2({
                        data:hub_data,
                        placeholder: "Select Hub",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $("#hub").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Hub",
                width:'100%',
                dropdownParent: $('#AddConsigneeModal'),
            });

            $("#edit_hub").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Hub    ",
                width:'100%',
                dropdownParent: $('#EditConsigneeModal'),
            });

            $('body').on('click','#datatable button.edit',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                var consignee = $(this).parents('tr').find('td.consignee_name').attr('data-consignee_id');
                var hub = $(this).parents('tr').find('td.hub_name').attr('data-hub_id');
                if(id && consignee && hub){
                    $('#edit_petty_cash_consignee_id').val(id);
                    $('#edit_consignee').val(consignee);
                    $('#edit_hub').val(hub);
                    $('#edit_hub').trigger("change");
                    $(".myError").html('');
                    $('#EditConsigneeModal').modal('show');
                }else{
                    var error = 'Petty Cash Consignee Information Not Found, Please Try again!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });

            $('body').on('click','#addConsignee', function () {
                var consignee = $('#consignee').val();
                var hub = $('#hub').val();
                $(".myError").html('');
                if(consignee == '')
                {
                    $("#consignee_error").html('Consignee is Required');
                    return;
                }

                if(hub == '')
                {
                    $("#hub_error").html('Hub is Required');
                    return;
                }

                $.ajax({
                    url: '{!! route('admin.settings.petty_cash.consignee.store') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'consignee': consignee,
                        'hub': hub,
                    }
                }).done(function(data){
                    if(data.status){
                        table.draw(true);
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        $('#consignee').val('');
                        $('#hub').val('');
                        $('#hub').trigger('change');
                        $('#AddConsigneeModal').modal('hide');
                    }else{
                        if(data.consignee_error)
                        {
                            $("#consignee_error").html(data.consignee_error);
                        }

                        if(data.hub_error)
                        {
                            $("#hub_error").html(data.hub_error);
                        }
                    }
                });
            });

            $('body').on('click','#editConsignee', function () {
                var consignee = $('#edit_consignee').val();
                var hub = $('#edit_hub').val();
                var id = $("#edit_petty_cash_consignee_id").val();
                $(".myError").html('');
                if(consignee == '')
                {
                    $("#edit_consignee_error").html('Consignee is Required');
                    return;
                }

                if(hub == '')
                {
                    $("#edit_hub_error").html('Hub is Required');
                    return;
                }

                if(id == '')
                {
                    var error = 'Petty Cash Consignee Information Not Found, Please Try again!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    $('#EditConsigneeModal').modal('hide');
                    return;
                }

                $.ajax({
                    url: '{!! route('admin.settings.petty_cash.consignee.edit') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'consignee': consignee,
                        'hub': hub,
                        'id' : id,
                    }
                }).done(function(data){
                    if(data.status){
                        table.draw(true);
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        $('#EditConsigneeModal').modal('hide');
                    }else{

                        if(data.hub_error)
                        {
                            $("#edit_hub_error").html(data.hub_error);
                        }

                        if(data.error)
                        {
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    }
                });
            });
        });
    </script>
@endsection