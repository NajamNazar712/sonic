@extends('client.layout.master')

@section('title', 'Welcome')

@section('content')
    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body text-center">
                <h1 class="mb-5">Welcome to Sonic..</h1>
                <div class="row justify-content-center">
                    <div class="col-8">
                        <table class="table table-bordered">
                            @if(count($sales_person_data)> 0)
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1"><b>Sales Person Name</b></th>
                                        <th class="border-primary border-darken-1"><b>Sales Person Phone</b></th>
                                        <th class="border-primary border-darken-1"><b>Sales Person Email</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><h4>{{$sales_person_data['name']}}</h4></td>
                                        <td><h4>{{$sales_person_data['phone']}}</h4></td>
                                        <td><h4>{{$sales_person_data['email']}}</h4></td>
                                    </tr>
                                </tbody>
                            @endif
                            @if(count($poc)> 0)
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1"><b>POC Name</b></th>
                                        <th class="border-primary border-darken-1"><b>POC Phone</b></th>
                                        <th class="border-primary border-darken-1"><b>POC Email</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($poc as $p)
                                    <tr>
                                        <td><h4>{{$p['name']}}</h4></td>
                                        <td><h4>{{$p['phone']}}</h4></td>
                                        <td><h4>{{$p['email']}}</h4></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @endif
                            @if(count($kam)> 0)
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1"><b>KAM Name</b></th>
                                        <th class="border-primary border-darken-1"><b>KAM Phone</b></th>
                                        <th class="border-primary border-darken-1"><b>KAM Email</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($kam as $k)
                                    <tr>
                                        <td><h4>{{$k['name']}}</h4></td>
                                        <td><h4>{{$k['phone']}}</h4></td>
                                        <td><h4>{{$k['email']}}</h4></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @endif
                        </table>
                        @if(count($pickup_riders)> 0)
                        <h2>Pickup Courier Details</h2>
                        <table class="table table-bordered">

                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1"><b>Courier Name</b></th>
                                    <th class="border-primary border-darken-1"><b>Courier Phone</b></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($pickup_riders as $rider)
                                    <tr>
                                        <td><h4>{{$rider->name}}</h4></td>
                                        <td><h4>{{$rider->phone}}</h4></td>
                                    </tr>
                                @endforeach
                                </tbody>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
