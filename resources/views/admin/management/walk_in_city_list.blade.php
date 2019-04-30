@extends('admin.layout.master')

@section('title', 'Walk-In City List')

@section('content')
    <h1>Walk-In City List</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="row text-center">
                                <div class="col">
                                    <table class="table table-bordered display-inline mr-2">
                                        <thead>
                                        <tr role="row" class="bg-primary white text-center">
                                            <th colspan="2" class="border-primary border-darken-1">Overnight</th>
                                        </tr>
                                        <tr role="row" class="bg-primary bg-lighten-1 white">
                                            <th class="text-center border-primary border-lighten-2">ID</th>
                                            <th class="border-primary border-lighten-2">Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($cities as $city)
                                            @foreach($walk_in_cities as $walk_in_city)
                                                @if($city->id == $walk_in_city->city_id && $walk_in_city->delivery == 1)
                                            <tr role="row">
                                                <td class="text-center">{{ $city->id }}</td>
                                                <td>{{ $city->name }}</td>
                                            </tr>
                                                @endif
                                            @endforeach
                                        @endforeach
                                        </tbody>
                                    </table>

                                    <table class="table table-bordered display-inline mr-2">
                                        <thead>
                                        <tr role="row" class="bg-primary white text-center">
                                            <th colspan="2" class="border-primary border-darken-1">Overland</th>
                                        </tr>
                                        <tr role="row" class="bg-primary bg-lighten-1 white">
                                            <th class="text-center border-primary border-lighten-2">ID</th>
                                            <th class="border-primary border-lighten-2">Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($cities as $city)
                                            @foreach($walk_in_cities as $walk_in_city)
                                                @if($city->id == $walk_in_city->city_id && $walk_in_city->delivery == 2)
                                            <tr role="row">
                                                <td class="text-center">{{ $city->id }}</td>
                                                <td>{{ $city->name }}</td>
                                            </tr>
                                                @endif
                                            @endforeach
                                        @endforeach
                                        </tbody>
                                    </table>

                                    <table class="table table-bordered display-inline mr-2">
                                        <thead>
                                        <tr role="row" class="bg-primary white text-center">
                                            <th colspan="2" class="border-primary border-darken-1">Detain</th>
                                        </tr>
                                        <tr role="row" class="bg-primary bg-lighten-1 white">
                                            <th class="text-center border-primary border-lighten-2">ID</th>
                                            <th class="border-primary border-lighten-2">Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($cities as $city)
                                            @foreach($walk_in_cities as $walk_in_city)
                                                @if($city->id == $walk_in_city->city_id && $walk_in_city->delivery == 3)
                                            <tr role="row">
                                                <td class="text-center">{{ $city->id }}</td>
                                                <td>{{ $city->name }}</td>
                                            </tr>
                                                @endif
                                            @endforeach
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection