@extends('client.layout.master')

@section('title', 'Welcome')

@section('content')
    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body text-center">
                <h1>Welcome to Sonic..</h1>
                <h2>{{$quote}}</h2>
            </div>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('js')
@endsection