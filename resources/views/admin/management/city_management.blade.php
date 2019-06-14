@extends('admin.layout.master')

@section('title', 'City Management')

@section('content')
    <h1>City Management</h1>

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
                                    <th class="border-primary border-darken-1">City Name</th>
                                    <th class="border-primary border-darken-1">City Code</th>
                                    <th class="border-primary border-darken-1">Hub Name</th>
                                    <th class="border-primary border-darken-1">Hub Code</th>
                                    <th class="border-primary border-darken-1">Zone</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Updated By</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div style="display: none;">
                        <form id="city_active_form" action="{{route('admin.management.city.status')}}" method="post" class="mt-2">
                            {{csrf_field()}}
                            <input type="hidden" name="_method" value="PUT">
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/custom.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/forms/checkbox-radio.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.management.city.ajax') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('City Name');
                            head.push('City Code');
                            head.push('Hub Name');
                            head.push('Hub Code');
                            head.push('Zone');
                            head.push('Status');
                            head.push('Updated By');
                            head.push('Updated At');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.city_id);
                                row.push(values.hub);
                                row.push(values.hub_id);
                                row.push(values.zone);
                                row.push(values.status);
                                row.push(values.updated_by);
                                row.push(values.updated_at);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
           var table =  $('.datatable').DataTable({
               dom: '<"d-inline-block"l><"pull-right"B>tipr',
                @if (session('role_id') == 1 || in_array(89, session('permissions')))

                    buttons: [{
                       text: '<i class="la la-map-marker"></i> Add City',
                       className: 'btn btn-primary',
                       enabled: true,
                       action: function (e, dt, node, config) {
                            $('#addCity').modal('show');
                           var $invoker = $(e.relatedTarget);
                           var action = 'addcity';

                           if(action === 'addcity'){
                               $.get( "/admin/management/city/form", function( data ) {
                                   $("#addCityDiv").html(data);
                               });
                           }
                       }

                    },{
                    extend: 'excel',
                    title: 'City Management',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'],
                @else
                buttons: [{
                    extend: 'excel',
                    title: 'City Management',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },
                'reset'],
                @endif
                scrollX: true, scrollY: '350px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.management.city.ajax') }}',
                order: [[2, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'cities.name', class: 'align-middle city'},
                    {data: 'city_id', name: 'cities.id', class: 'align-middle city_id'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'hub_id', name: 'cities.hub_id', class: 'align-middle hub_id'},
                    {data: 'zone', name: 'z.name', class: 'align-middle zone'},
                    {data: 'status', name: 'cities.status', class: 'align-middle status'},
                    {data: 'updated_by', name: 'a.name', class: 'align-middle updated_by'},
                    {data: 'updated_at', name: 'ch.created_at', class: 'align-middle updated_at'},
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
                   var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                       '<option value="0">Inactive</option>' +
                       '<option value="1">Active</option>' +
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

            $('input.icheck').iCheck({
                checkboxClass: 'icheckbox_squaret-red',
                radioClass: 'iradio_square-red'
            });
        });

        $("#addCity").on("show.bs.modal", function(e) {
            var $invoker = $(e.relatedTarget);
            var action = $invoker.attr('rel');

            if(action == 'addcity'){
                $.get( "/admin/management/city/form", function( data ) {
                    $("#addCityDiv").html(data);
                });
            }

        });
        $("#editCity").on("show.bs.modal", function(e) {
            var $invoker = $(e.relatedTarget);
            var action = $invoker.attr('rel');
            var id = $(e.relatedTarget).data('target-id');
            

            if(action == 'editcity'){
                $.get( "/admin/management/city/"+id+"/edit/form", function( data ) {
                    $("#editCityDiv").html(data);
                });
            }
        });

        $('body').on('click','.deactivate',function (e) {
            var id = $(this).data('target-id');
            var rel = $(this).attr('rel');
            var isHub = $(this).attr('hub');

            if(isHub == 0){
                $('#city_active_form #cid').val(id);
                $('#city_active_form #cstatus').val(rel);
                if(rel == 'cityInactive'){
                    var atext = "Select Yes to Deactive this city!";
                }else{
                    var atext = "Select Yes to active this city!";
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
                        $('#city_active_form').submit();
                    }
                });
                // $('#ConfirmModalCity').modal('show');
            }else if(isHub == 1){
                if(rel == 'cityactive'){
                    $('#city_active_form #cid').val(id);
                    $('#city_active_form #cstatus').val(rel);
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
                            $('#city_active_form').submit();
                        }
                    });
                }else{
                    $.ajax({
                        url:'/admin/management/city/'+id+'/status/ajax',
                        type:'GET',
                        dataType:'json',
                        success:function (data) {
                            var name = [];
                            if(data.length > 0){
                                var comma = '';
                                $.each(data, function (index, value) {
                                    if(data.length != index+1){ comma = ", ";}else{
                                        comma = '';
                                    }
                                    name += value.name+comma;

                                });
                                swal({
                                    title: 'Please remove following cities from hub!',
                                    text: name,
                                    icon: 'info',
                                    buttons: {
                                        cancel: {
                                            text: 'Close',
                                            value: null,
                                            visible: true,
                                            closeModal: true,
                                        }
                                    },
                                    closeOnClickOutside: true,
                                    closeOnEsc: true
                                });

                            }else{
                                $('#city_active_form #cid').val(id);
                                $('#city_active_form #cstatus').val(rel);
                                if(rel == 'cityInactive'){
                                    var atext = "Select Yes to Deactive this Hub!";
                                }else{
                                    var atext = "Select Yes to active this Hub!";
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
                                        $('#city_active_form').submit();
                                    }
                                });
                            }

                        }
                    });
                }

            }

        });



    </script>

@endsection