@extends('admin.layout.master')

@section('content')
    <h1>Pending Accounts List</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Pending Accounts</h4>
                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered zero-configuration">
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
                                <tbody>
                                @foreach($accounts as $account)
                                    <tr>
                                        <td>{{$account->id}}</td>
                                        <td>{{$account->name}}</td>
                                        <td>{{$account->city_code}}</td>
                                        <td>{{$account->poc}}</td>
                                        <td>{{$account->phone}}</td>
                                        <td>{{$account->address}}</td>
                                        <td>{{$account->email}}</td>
                                        <td><a href="#" data-target-id="{{$account->id}}" data-toggle="modal" data-target="#BankInfoModal">View Bank Info</a></td>
                                    </tr>
                                @endforeach
                                </tbody>
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


@endsection