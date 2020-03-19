@extends('client.layout.master')

@section('title', 'Report - Delivery & Return')

@section('content')
    <h1 class="mb-1">
        Report - Delivery & Return
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('client.inc.messages')

                <div id="powerbi">
                    <iframe src="{{ $link }}" frameborder="0" allowFullScreen="false"></iframe>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <style type="text/css">
      #powerbi {
        margin: auto;
        padding: 0;
        width: 600px;
        height: 500px;
        position: relative;
        overflow: hidden;
      }
      #powerbi iframe {
        border: none;
        width: 100%;
        height: calc(100% + 37px);
        position: absolute;
      }
    </style>
@endsection