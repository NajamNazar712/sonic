@extends('admin.layout.master')

@section('title', 'Lost Shipment Admins')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Lost Shipment Admins
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">User Name</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade text-left" id="AddAdminModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddAdminModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Add User</h4>
                </div>
                <form method="post" id="add_admin" action="{{route('admin.settings.lost_shipment_admins.add')}}">
                    @csrf

                <div class="modal-body">
                    <div class="form-group">
                        <select name="admin_id" id="admin_id" class="form-control select2" data-rule-required="true" data-msg-required="Shipper ID is required">
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}" > {{ $admin->name }} </option>
                            @endforeach
                        </select>
                    </div>
                   
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" >Add</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
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
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {

            $('#AddAdminModal').on('hidden.bs.modal', function () {
                $('#admin_id').val('').trigger('change.select2');
            });
           
            $('#admin_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select User",
                allowClear:true,
                dropdownParent:$('#add_admin')
            });
          
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[
                    @if (session('role_id') == 1 || in_array(710, session('permissions')))
                    
                    {
                        text: '<i class="la la-plus"></i> Add',
                        className: 'btn btn-primary tag_agents',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#AddAdminModal').modal('show');
                            
                        }
                    },
                    @endif
                    'reset'
                    ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                language: {
                    processing: data_table_loader
                },
                ajax: '{{ route('admin.settings.lost_shipment_admins.list') }}',
                rowId: 'id',
                order: [[0, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'admin_name', name: 'ad.name', class: 'align-middle shipper_name'},
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

           
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.delete', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                swal({
                        text: 'Are you sure, you want to Delete?',
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
                        }).then(function(confirm) {

                            
                            $.ajax({
                            url:'{!! route("admin.settings.lost_shipment_admins.delete") !!}',
                            method: 'POST',
                            data: {
                                'id': id,
                                '_token': '{{ csrf_token() }}'
                            }
                            }).done(function (data) {
                                toastr.success(data.success, 'Success!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                                table.draw();
                            });
                        });
                
            });
            
            $( "#add_admin" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    form.submit();    
                }
                
                });

        });
    </script>
@endsection