@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/custom.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

    <style type="text/css">
        .radio-inline,.checkbox-inline{
            display:inline;
        }
    </style>
@endsection
@section('content')
    <h1>City Management</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <span class="font-large-1 card-title">Cities List</span>
                        <button type="button" rel="addcity" class="btn btn-primary btn-min-width mr-1 mb-1 pull-right" data-target="#addCity" data-toggle="modal">Add City</button>

                        <div class="mt-1">
                            @include('admin.inc.messages')
                        </div>
                    </div>


                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered datatable" id="datatable">
                                <thead>
                                <tr>
                                    <th>S No.</th>
                                    <th>City Name</th>
                                    <th>City Code</th>
                                    <th>Hub Name</th>
                                    <th>Hub Code</th>
                                    <th>Status</th>
                                    {{--<th>Services Available</th>--}}
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

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/forms/checkbox-radio.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
           var tab =  $('.datatable').DataTable({
                dom: 'ltipr',
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
                    {data:'id', defaultContent:'', searchable: false},
                    {data: 'name', name: 'name', class: 'city'},
                    {data: 'id', name: 'id', class: 'city_id'},
                    {data: 'hub', name: 'hub', class: 'hub'},
                    {data: 'hub_id', name: 'hub_id', class: 'hub_id'},
                    {data: 'status', name: 'status', class: 'status'},
                    {data: 'action', name: 'action', class: 'action', orderable: false, searchable: false}
                ],

                initComplete: function() {
                    var search = $('<tr role="row" class="search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:0;"></td>';
                    var input = '<input type="text" placeholder="Search" style="width:100%;" />';
                    var select = '<select style="width:100%;"><option value=""></option></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.action')) {
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('keyup change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                }
            });
            if (tab.data().length != 0) {
                tab.on('order.dt search.dt', function () {
                    tab.column(0, {search: 'false', order: 'applied'}).nodes().each(function (cell, i) {
                        cell.innerHTML = i + 1;
                        tab.cell(cell).invalidate('dom');
                    });
                }).draw();
            }

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
            console.log(id);
            if(action == 'editcity'){
                $.get( "/admin/management/city/"+id+"/edit/form", function( data ) {
                    $("#editCityDiv").html(data);
                });
            }

        });
        $("#ConfirmModalCity").on("show.bs.modal", function(e) {
            var id = $(e.relatedTarget).data('target-id');
            var rel = $(e.relatedTarget).attr('rel');
            console.log('here');
            $('#cid').val(id);
            $('#cstatus').val(rel);


        });
    </script>

@endsection