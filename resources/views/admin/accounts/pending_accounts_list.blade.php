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
                                        <td>{{$account->city->city_name}}</td>
                                        <td>{{$account->poc}}</td>
                                        <td>{{$account->phone}}</td>
                                        <td>{{$account->address}}</td>
                                        <td>{{$account->email}}</td>
                                        <td>
                                            <span class="dropdown">
                                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false"><i class="ft-settings"></i></button>
                                            <div class="dropdown-menu open-left arrow">
                                              <a href="#" class="dropdown-item" data-target-id="{{$account->id}}" data-toggle="modal" data-target="#BankInfoModal"><i class="ft-plus-circle primary"></i> View Bank Info</a>
                                              <a href="#" class="dropdown-item" data-target-id="{{$account->id}}" data-toggle="modal" data-target="#ShippingInfoModal"><i class="ft-plus-circle primary"></i> View Shipping Info</a>
                                                {{--<div class="dropdown-divider"></div>--}}
                                              <a href="{{route('admin.add.rates',['id'=> $account->id])}}" class="dropdown-item"><i class="ft-plus-circle primary"></i> Add Rates</a>

                                            </div>
                                          </span>
                                            {{--<a href="#" data-target-id="{{$account->id}}" data-toggle="modal" data-target="#BankInfoModal">View Bank Info</a><br>--}}
                                            {{--<a href="#" data-target-id="{{$account->id}}" data-toggle="modal" data-target="#ShippingInfoModal">View Shipping Info</a>--}}
                                        </td>
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