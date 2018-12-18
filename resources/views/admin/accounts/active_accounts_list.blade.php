@extends('admin.layout.master')

@section('title', 'Active Accounts List')

@section('content')
    <h1>Active Accounts List</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">

                        @include('admin.inc.messages')

                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3">
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No</th>
                                        <th class="border-primary border-darken-1">Account ID</th>
                                        <th class="border-primary border-darken-1">Company Name</th>
                                        <th class="border-primary border-darken-1">City Name</th>
                                        <th class="border-primary border-darken-1">Contact Person</th>
                                        <th class="border-primary border-darken-1">Phone Number</th>
                                        <th class="border-primary border-darken-1">Address</th>
                                        <th class="border-primary border-darken-1">Email Address</th>
                                        <th class="border-primary border-darken-1">Product Type</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1">Sales person tagged</th>
                                        <th class="border-primary border-darken-1">Request Date</th>
                                        <th class="border-primary border-darken-1">Rate Added By</th>
                                        <th class="border-primary border-darken-1">Rate Approved By</th>
                                        <th class="border-primary border-darken-1">Account Activated By</th>
                                        <th class="border-primary border-darken-1">Account Activation Date</th>
                                        <th class="border-primary border-darken-1">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade text-left" id="SalesTagModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SalesTagModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag Sales Person</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="shipper_id">
                    <select name="Sale_person" id="saletag" class="form-control select2">
                        @foreach($sale_name as $sn)
                            <option value="{{ $sn->id }}" > {{ $sn->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="salesTagSubmit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">



@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
    $(document).ready(function() {
        jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            if ( this.context.length ) {
                body = [];

                var jsonResult = $.ajax({
                    url: '{{ route('admin.accounts.active.ajax') }}',
                    data: {
                        'page': 'all',
                    },
                    success: function (result) {
                        head = [];

                        head.push('S.No');
                        head.push('Account ID');
                        head.push('Company Name');
                        head.push('City Name');
                        head.push('Contact Person');
                        head.push('Phone No.');
                        head.push('Company Address');
                        head.push('Email Address');
                        head.push('Product Type');
                        head.push('Status');
                        head.push('Sales person tagged');
                        head.push('Request Date');
                        head.push('Rates Added By');
                        head.push('Rates Approved By');
                        head.push('Account Activated By');
                        head.push('Account Activation Date');
                        $.each(result.data, function(index, values) {
                            row = [];


                            row.push(index + 1);
                            row.push(values.id_padded);
                            row.push(values.name);
                            row.push(values.city);
                            row.push(values.poc);
                            row.push(values.phone);
                            row.push(values.address);
                            row.push(values.email);
                            row.push(values.product_name);
                            row.push(values.status);
                            row.push(values.admin_tag_id);
                            row.push(values.created_at);
                            row.push(values.added_by);
                            row.push(values.approved_by);
                            row.push(values.account_activated_by);
                            row.push(values.activated_date);

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
           scrollX: true, scrollY: '350px',
           buttons: [
               {
                   extend: 'excel',
                   title: 'Active Accounts',
                   text: '<i class="la la-file-excel-o"></i> Excel',
               },
           ],
            lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
            pageLength: 50,
            pagingType: 'full_numbers',
            processing: true,
            serverSide: true,
            rowId: 'id',
            order: [[1, 'desc']],
            ajax: '{{ route('admin.accounts.active.ajax') }}',
            columns: [
                {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'id_padded', name: 'users.id', class: 'align-middle account_id'},
                {data: 'name', name: 'name', class: 'align-middle company_name'},
                {data: 'city', name: 'cities.name', class: 'align-middle city'},
                {data: 'poc', name: 'poc', class: 'align-middle contact_person'},
                {data: 'phone', name: 'phone', class: 'align-middle phone'},
                {data: 'address', name: 'address', class: 'align-middle address'},
                {data: 'email', name: 'email', class: 'align-middle email'},
                {data: 'product_type', name: 'product_type', class: 'align-middle product_type'},
                {data: 'status', name: 'status', class: 'align-middle status'},
                {data: 'admin_tag_id', name: 'ad.name', class: 'align-middle admin_tag_id'},
                {data: 'created_at', name: 'users.created_at', class: 'align-middle created_at'},
                {data: 'added_by', name: 'rab.name', class: 'align-middle added_by'},
                {data: 'approved_by', name: 'rabb.name', class: 'align-middle approved_by'},
                {data: 'account_activated_by', name: 'rabba.name', class: 'align-middle account_activated_by'},
                {data: 'activated_date', name: 'users.activated_at', class: 'align-middle activated_date'},
                {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false}
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
                    '<option value="3">Enable</option>' +
                    '<option value="4">Disable</option>' +
                    '</select>';
                var product_select = '<select name="product_select" id="product_select" class="select2 form-control"></select>';

                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();

                    if ($(header).is('.action')  || $(header).is('.serial_number')) {
                        $(td).appendTo($(search));
                    }else if($(header).is('.status')){
                        $(drop_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }else if($(header).is('.product_type')){
                        $(product_select).appendTo($(search))
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
                    placeholder: "Select a Status",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                var data1 = $.map({!! $products !!}, function (obj) {
                    obj.id = obj.id // replace pk with your identifier

                    return obj;
                });
                var data1 = $.map({!! $products !!}, function (obj) {
                    obj.text = obj.product_name; // replace name with the property used for the text

                    return obj;
                });

                $("#product_select").prepend('<option value="" selected></option>').select2({
                    data:data1,
                    placeholder: "Select Product",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                this.api().table().columns.adjust();
            }
        });
        $('body').on('change','.blacklist_reason',function() {
            $(this).val($(this).val().trim());
        });
        $('body').on('click','button.blacklist',function () {
            var id = $(this).parents('tr').attr('id');
            var status = $(this).attr('rel');
            swal({
                // title: 'Are You Sure?',
                text: 'Write a reason to blacklist this account!',
                content: {
                    element: "input",
                    attributes: {
                        placeholder: "Write a reason",
                        class: "form-control blacklist_reason",
                    },
                },
                buttons: {
                    cancel: {
                        text: 'No',
                        value: false,
                        visible: true,
                        closeModal: true,
                    },
                    confirm: {
                        text: 'Yes',
                        value: true,
                        visible: true,
                        closeModal: false
                    }
                },
                closeOnClickOutside: false,
                closeOnEsc: false,
                dangerMode: true
            }).then((value) => {
                    if (value) {
                        if (value === '') {
                            swal("You have not selected any reason!", {
                                icon: "warning",
                            });
                        } else {
                        if (id) {
                            $.ajax({
                                url: '{!! route('admin.accounts.status.block') !!}',
                                method: 'POST',
                                data: {
                                    'id': id,
                                    'reason': value,
                                    'status': status,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                swal.close();
                                if (data.status === 1) {
                                    table.draw('false');
                                    swal.close();
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }

                            });
                        }
                    }
                    }else{
                        swal.close();
                    }

            });


        });
        $("#saletag").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Sales Person",
            width:'100%',
            dropdownParent:$('#SalesTagModal')
        });
        $('#SalesTagModal').on('shown.bs.modal',function (e) {
            var $invoker = $(e.relatedTarget);
            var shipper_id = $invoker.data('target-id');
            $('#shipper_id').val(shipper_id);
        });
        $('#salesTagSubmit').on('click',function () {
            var shipper = $('#shipper_id').val();
            var tag = parseInt($('#saletag').val());
            if(tag){
                $.ajax({
                    url: '{!! route('admin.accounts.tag.submit') !!}',
                    method: 'POST',
                    data: {
                        'admin_id': tag,
                        'shipper_id':shipper,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        if(data.status){
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }
                        else {
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        $('#saletag').val('').trigger('change');
                        $('#SalesTagModal').modal('hide');
                    });
            }else{
                var error = "Sales Person Not Selected!";
                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            }

        });
        $('body').on('click','button.userenable',function () {
            var status  = "enable";
            var id = $(this).parents('tr').attr('id');
            swal({
                title: 'Are You Sure?',
                text: 'Select Yes to enable this account!',
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
                if(confirm){
                    if(id){
                        $.ajax({
                            url: '{!! route('admin.accounts.status.change') !!}',
                            method: 'POST',
                            data: {
                                'id':id,
                                'status':status,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status === 1){
                                table.draw('false');
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }

                        });
                    }
                }
            });

        });
        $('body').on('click','button.userdisable',function () {
            var status  = "disable";
            var id = $(this).parents('tr').attr('id');
            swal({
                title: 'Are You Sure?',
                text: 'Select Yes to Disable this account!',
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
               if(confirm){
                   if(id){
                       $.ajax({
                           url: '{!! route('admin.accounts.status.change') !!}',
                           method: 'POST',
                           data: {
                               'id':id,
                               'status':status,
                               '_token': '{{ csrf_token() }}'
                           }
                       }).done(function (data) {
                           if(data.status == 1){
                               table.draw('false');
                               toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                           }else{
                               toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                           }

                       });
                   }
               }
            });

        });
    });

</script>

@endsection

