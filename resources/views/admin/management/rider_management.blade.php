@extends('admin.layout.master')

@section('title', 'Rider Management')

@section('content')
    <h1>Rider Management</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Name</th>
                                    <th class="border-primary border-darken-1">Phone No</th>
                                    <th class="border-primary border-darken-1">CNIC</th>
                                    <th class="border-primary border-darken-1">Address</th>
                                    <th class="border-primary border-darken-1">Route</th>
                                    <th class="border-primary border-darken-1">Category</th>
                                    <th class="border-primary border-darken-1">Added On</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                <div style="display: none;">
                    <form id="rider_active_form" action="{{route('admin.management.rider.status')}}" method="post" class="mt-2">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="cid" id="cid">
                        <input type="hidden" name="status" id="cstatus">
                        <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">Yes</button>
                        <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">Cancel</button>


                    </form>
                </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var table =  $('.datatable').DataTable({
                @if (session('role_id') == 1 || in_array(97, session('permissions')))
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    buttons: [{
                        text: 'Add Rider',
                        className: 'btn btn-primary',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#addRider').modal('show');

                        }

                    }],
                @else
                    dom: 'ltipr',
                @endif
                scrollX: true, scrollY: '300px',
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,

                ajax: '{{ route('admin.management.rider.ajax') }}',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'city', name: 'cities.name', class: 'align-middle city'},
                    {data: 'rider', name: 'riders.name', class: 'align-middle name'},
                    {data: 'phone', name: 'riders.phone', class: 'align-middle phone'},
                    {data: 'cnic', name: 'riders.cnic', class: 'align-middle cnic'},
                    {data: 'address', name: 'riders.address', class: 'align-middle address'},
                    {data: 'route', name: 'route', class: 'align-middle route'},
                    {data: 'category', name: 'rider_categories.name', class: 'align-middle category'},
                    {data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}
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


            $("#addRider").on("show.bs.modal", function(e) {
                $.get( "/admin/management/rider/add", function( data ) {
                    $("#addRiderDiv").html(data);
                });
            });
            $("#editRider").on("show.bs.modal", function(e) {

                var id = $(e.relatedTarget).data('target-id');

                $.get( "/admin/management/rider/"+id+"/edit", function( data ) {
                    $("#editRiderDiv").html(data);
                });

            });

            $('body').on('click','.deactivate',function (e) {
                var id = $(this).data('target-id');
                var rel = $(this).attr('rel');

                $('#rider_active_form #cid').val(id);
                $('#rider_active_form #cstatus').val(rel);
                if(rel == 'riderInactive'){
                    var atext = "Select Yes to Deactive this Rider!";
                }else{
                    var atext = "Select Yes to active this Rider!";
                }
                swal({
                    title: 'Are You Sure?',
                    text: atext,
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
                        $('#rider_active_form').submit();
                    }
                });

            });
        });
    </script>

@endsection