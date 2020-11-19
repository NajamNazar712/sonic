@extends('client.layout.master')

@section('title', 'Welcome')

@section('content')
    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body text-center">
                <h1 class="mb-5">Welcome to Sonic..</h1>
                @if(count($sales_person_data)> 0)
                <div class="row justify-content-center">
                    <div class="col-6">
                        <table class="table table-bordered">
                            <thead>
                            <td><b>Sales Person Name</b></td>
                            <td><b>Sales Person Phone</b></td>
                            </thead>
                            <tbody>
                            <tr>
                                <td><h4>{{$sales_person_data['name']}}</h4></td>
                                <td><h4>{{$sales_person_data['phone']}}</h4></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @endif
            </div>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('js')
@endsection