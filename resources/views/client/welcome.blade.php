@extends('client.layout.master')

@section('title', 'Welcome')

@section('content')
    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body text-center">
                <h1 class="mb-5">Welcome to Sonic..</h1>
                @if(count($sales_person_data)> 0)
                    <div class="row justify-content-center">
                        <div class="col-8">
                            <table class="table table-bordered">
                                <thead>
                                <td><b>Sales Person Name</b></td>
                                <td><b>Sales Person Phone</b></td>
                                <td><b>Sales Person Email</b></td>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><h4>{{$sales_person_data['name']}}</h4></td>
                                    <td><h4>{{$sales_person_data['phone']}}</h4></td>
                                    <td><h4>{{$sales_person_data['email']}}</h4></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                <div class="row justify-content-center">
                    <div class="col-8">
                        <table class="table table-bordered">
                            @if(count($poc)> 0)
                                <thead>
                                    <tr>
                                        <td><b>POC Name</b></td>
                                        <td><b>POC Phone</b></td>
                                        <td><b>POC Email</b></td>
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
                                    <tr>
                                        <td><b>KAM Name</b></td>
                                        <td><b>KAM Phone</b></td>
                                        <td><b>KAM Email</b></td>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
