@extends('admin.layout.master')

@section('title', 'All Employees List')

@section('content')
    <h1>Active Employees List</h1>

    <section>
        <div class="row">
            
            <div class="col-12">
                <div class="card">
                    <h2 class="heading_user">All Employees</h2>
                    @include('admin.inc.messages')
    
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No</th>
                                        <th class="border-primary border-darken-1">Trax ID</th>
                                        <th class="border-primary border-darken-1">Name</th>
                                        <th class="border-primary border-darken-1">CNIC</th>
                                        <th class="border-primary border-darken-1">Phone No.</th>
                                        <th class="border-primary border-darken-1">Employee Role</th>
                                        <th class="border-primary border-darken-1">Created at</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <style>
    .heading_user{
        margin-top:50px;
        padding-left:35px;
    }
    </style>

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
    $(function () {
    
        jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.human_resource.all_user_ajax') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            footer = [];

                            head.push('S. No');
                            head.push('Trax ID');
                            head.push('Name');
                            head.push('CNIC');
                            head.push('Phone No.');
                            head.push('Employee Role');
                            head.push('Created at');
                            $.each(result.data, function(index, values) {
                                if(values.id!=null){

                                row = [];
                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.name);
                                row.push(values.cnic);
                                row.push(values.phone);
                                row.push(values.role);
                                row.push(values.created_at);
                                body.push(row);
                            }

                            });
                         
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            });


    var table = $('#datatable').DataTable({
        dom: '<"d-inline-block"l><"pull-right"B>tipr',
        "order": [[ 6, "desc" ]],
                scrollX: false, scrollY: '500px',
                    buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'All Employees List',
                        text: '<i class="la la-file-excel-o"></i> Excel',

                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                language: {
                    processing: data_table_loader
                },
                
        ajax:{
                    url: '{{ route('admin.human_resource.all_user_ajax') }}'
                   
                },
        columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'trax_id', name: 'trax_id', class: 'align-middle trax_id'},
                    {data: 'name', name: 'name', class: 'align-middle name'},
                    {data: 'cnic', name: 'cnic', class: 'align-middle cnic'},
                    {data: 'phone', name: 'phone', class: 'align-middle phone'},
                    {data: 'role', name: 'role', class: 'align-middle role'},
                    {data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
                ],rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    console.log(data['id']);
                    if(data['id']!=null){

                        $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    }
                },initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var role_select = '<select name="role_select" id="role_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.role')){
                            $(role_select).appendTo($(search))
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
                    var data1 = $.map({!! $roles !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                 

                    $("#role_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Role",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
    });

    
  });

</script>

@endsection

