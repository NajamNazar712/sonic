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
                            <table class="datatable table table-stripped table-bordered zero-configuration" id="datatable">
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
                                <tfoot>
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
                                </tfoot>

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
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.accounts.block.ajax') }}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'city', name: 'city'},
                {data: 'poc', name: 'poc'},
                {data: 'phone', name: 'phone'},
                {data: 'address', name: 'address'},
                {data: 'email', name: 'email'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
              initComplete: function () {
                    var r = $('#datatable tfoot tr');
                    $('#datatable thead').append(r);
            this.api().columns().every(function () {
                var column = this;
                var input = document.createElement("input");
                $(input).appendTo($(column.footer()).empty())
                .on('change', function () {
                    column.search($(this).val(), false, false, true).draw();
                });
            });
        }
    });
    });

</script>

@endsection

