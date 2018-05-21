@extends('admin.layout.master')

@section('css')

@endsection
@section('content')
    <h1>City Management</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h2 class="font-large-1">Cities List</h2>
                        @include('admin.inc.messages')
                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered datatable" id="datatable">
                                <thead>
                                <tr>
                                    <th>City Name</th>
                                    <th>CityCode</th>
                                    <th>City Name</th>
                                    <th>Contact Person</th>
                                    <th>Phone Number</th>
                                    <th>Address</th>
                                    <th>Email Address</th>
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


@endsection