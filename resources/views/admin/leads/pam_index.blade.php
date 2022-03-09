@extends('admin.layout.master')

@section('title', 'Movit Lead Management')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Movit Lead Management
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;width: 100%">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Lead ID</th>
                                    <th class="border-primary border-darken-1">Name</th>
                                    <th class="border-primary border-darken-1">Contact No.</th>
                                    <th class="border-primary border-darken-1">Category</th>
                                    <th class="border-primary border-darken-1">Items</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Images</th>
                                    <th class="border-primary border-darken-1">Include</th>
                                    <th class="border-primary border-darken-1">Video Link</th>
                                    <th class="border-primary border-darken-1">Lead Creation Date</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="showLeadItems" role="dialog" aria-labelledby="showLeadItems" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_mapping_title">Lead Items</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered" id="items_table">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S.No</th>
                                    <th class="border-primary border-darken-1">Name</th>
                                    <th class="border-primary border-darken-1">Quantity</th>
                                    <th class="border-primary border-darken-1">Length (cm)</th>
                                    <th class="border-primary border-darken-1">Width (cm)</th>
                                    <th class="border-primary border-darken-1">Height (cm)</th>
                                    <th class="border-primary border-darken-1">Weight (kg)</th>
                                </tr>
                                </thead>
                            </table>
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
        $(document).ready(function() {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.pam_leads.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Lead ID');
                            head.push('Name');
                            head.push('Contact No');
                            head.push('Category');
                            head.push('Items');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Include');
                            head.push('Video Link');
                            head.push('Lead Creation Date');
                            

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.lead_id);
                                row.push(values.name);
                                row.push(values.phone);
                                row.push(values.category);
                                row.push(values.item_count);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.case);
                                row.push(values.video_link);
                                row.push(values.lead_created_at);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Movit Lead Managements',
                        className:'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.pam_leads.list') }}',
                },
                rowId: 'id',
                order: [[11, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'pickup_notes.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'lead_id', name: 'pam_leads.lead_id', class: 'align-middle lead_id'},
                    {data: 'name', name: 'pam_leads.name', class: 'align-middle name'},
                    {data: 'phone', name: 'pam_leads.phone', class: 'align-middle phone'},
                    {data: 'category', name: 'pam_leads.location_type', class: 'align-middle category'},
                    {data: 'item_count_button', name: 'item_count', class: 'align-middle text-center item_count'},
                    {data: 'origin', name: 'o.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'd.name', class: 'align-middle destination'},
                    {data: 'images_link_btn', name:'', orderable: false, searchable: false, class: 'align-middle text-center images_link_btn'},
                    {data: 'case', name: 'pam_leads.case_type', class: 'align-middle case'},
                    {data: 'video_link_btn', orderable: false, searchable: false, name: '', class: 'align-middle text-center video_link_btn'},
                    
                    {data: 'lead_created_at', name: 'pam_leads.created_at', searchable: false, name: '', class: 'align-middle text-center lead_created_at'},
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
                    var category_select = '<select name="category_select" id="category_select" class="select2 form-control">' +
                        '</select>';
                    var case_select = '<select name="case_select" id="case_select" class="select2 form-control">' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.item_count') || $(header).is('.images_link_btn') || $(header).is('.video_link_btn') || $(header).is('.lead_created_at')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.category')){
                            $(category_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.case')){
                            $(case_select).appendTo($(search))
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

                    var category_data = [{'id':1,'text':'Home Shifting'},{'id':2,'text':'Office Shifting'}];
                    var case_data = [{'id':1,'text':'Both'},{'id':2,'text':'Packing'},{'id':3,'text':'Unpacking'}];

                    $("#category_select").prepend('<option value="" selected></option>').select2({
                        data:category_data,
                        placeholder: "Select Category",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    $("#case_select").prepend('<option value="" selected></option>').select2({
                        data:case_data,
                        placeholder: "Select Includes",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            var items_table = $("#showLeadItems #items_table").DataTable({
                dom: 'ltipr',
                paging:false,
                autoWidth: false,
                columns: [
                    {orderable: false, searchable: false, name: 'item_serial_number', class: 'align-middle item_serial_number'},
                    {name: 'item_name', class: 'align-middle item_name', orderable: false, searchable: false},
                    {name: 'quantity', class: 'align-middle quantity', orderable: false, searchable: false},
                    {name: 'length', class: 'align-middle length', sortable: false, orderable: false, searchable: false},
                    {name: 'width', class: 'align-middle width', sortable: false, orderable: false, searchable: false},
                    {name: 'height', class: 'align-middle height', sortable: false, orderable: false, searchable: false},
                    {name: 'weight', class: 'align-middle weight', sortable: false, orderable: false, searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = items_table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('body').on('click','.show_lead_items',function (){
                var lead_id = $(this).parents('tr').attr('id');
                $.ajax({
                    method:'Post',
                    url:'{{route("admin.pam_leads.items")}}',
                    data:{
                        '_token':"{{csrf_token()}}",
                        'id': lead_id,
                    },
                }).done(function (data){
                    items_table.rows().remove();
                    if(data.status == 1)
                    {
                        $.each(data.items,function (i,item){
                            let item_name;
                            if(item.item_id == 0)
                            {
                                item_name = "Other";
                            }
                            else{
                                item_name = item.item_name;
                            }

                            let length = item.length == 0 ? "-" : item.length;
                            let width = item.width == 0 ? "-" : item.width;
                            let height = item.height == 0 ? "-" : item.height;

                            items_table.row.add([0,item_name,item.quantity,length,width,height,item.weight]);
                        });
                        items_table.draw(true);
                        $("#showLeadItems").modal('show');
                    }
                    else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            });
        });
    </script>
@endsection