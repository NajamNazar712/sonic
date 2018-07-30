@extends('admin.layout.master')


@section('content')
    <h1>City Management</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <span class="font-large-1 card-title">Cities List</span>
                        {{--<button type="button" rel="addcity" class="btn btn-primary btn-min-width mr-1 mb-1 pull-right" data-target="#addCity" data-toggle="modal">Add City</button>--}}

                        <div class="mt-1">
                            @include('admin.inc.messages')
                        </div>
                    </div>


                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th>S No.</th>
                                    <th>City Name</th>
                                    <th>City Code</th>
                                    <th>Hub Name</th>
                                    <th>Hub Code</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
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


    <style type="text/css">
        table.dataTable {
            font-size: 12px;
        }

        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
            word-wrap: break-word;
        }

        table.dataTable thead tr th:before,
        table.dataTable thead tr th:after {
            height: 20px;
            margin-bottom: -10px;
            bottom: 50% !important;
        }

        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #666EE8;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }
    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/forms/checkbox-radio.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
           var table =  $('.datatable').DataTable({
               dom: '<"d-inline-block"l><"pull-right"B>tipr',
               buttons: [{
                   text: 'Add City',
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

               }],
                fixedHeader: {
                    header: true,
                    headerOffset: $('.header-navbar').height()
                },
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                stateSave: true,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.management.city.ajax') }}',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'cities.name', class: 'city'},
                    {data: 'city_id', name: 'cities.id', class: 'city_id'},
                    {data: 'hub', name: 'h.name', class: 'hub'},
                    {data: 'hub_id', name: 'cities.hub_id', class: 'hub_id'},
                    {data: 'status', name: 'status', class: 'status'},
                    {data: 'action', name: 'action', class: 'action', orderable: false, searchable: false}
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
            console.log(id)

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
                $('.modal-body #cid').val(id);
                $('.modal-body #cstatus').val(rel);
                $('#ConfirmModalCity').modal('show');
            }else if(isHub == 1){
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
                            $('.modal-body #cid').val(id);
                            $('.modal-body #cstatus').val(rel);
                            $('#ConfirmModalCity').modal('show');
                        }
                        
                    }
                });
            }

        });



    </script>

@endsection