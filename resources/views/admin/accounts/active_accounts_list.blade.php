@extends('admin.layout.master')

@section('content')
    <h1>Active Accounts List</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Active Accounts</h4>
                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered datatable" id="datatable">
                                <thead>
                                    <tr>
                                        <th>Account ID</th>
                                        <th>Company Name</th>
                                        <th>City</th>
                                        <th>Contact Person</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Email</th>
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







<script>
    $(document).ready(function() {
        $('.datatable').DataTable({
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
            ajax: '{{ route('admin.accounts.active.ajax') }}',
            columns: [
                {data: 'id', name: 'id', class: 'account_id'},
                {data: 'name', name: 'name', class: 'company_name'},
                {data: 'city_name', name: 'city', class: 'city'},
                {data: 'poc', name: 'poc', class: 'contact_person'},
                {data: 'phone', name: 'phone', class: 'phone'},
                {data: 'address', name: 'address', class: 'address'},
                {data: 'email', name: 'email', class: 'email'},
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

                    // if ($(header).is('.city')) {
                    //     var current = $(select).appendTo($(search)).on('change', function() {
                    //         var value = $.fn.dataTable.util.escapeRegex($(this).val());
                    //         column.search(value ? '^' + value + '$' : '', true, false).draw();
                    //     }).wrap(td);

                    //     column.data().unique().sort().each(function (d, j) {
                    //         current.append('<option value="' + d + '">' + d + '</option>')
                    //     });
                    // }
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
    });

</script>

@endsection

