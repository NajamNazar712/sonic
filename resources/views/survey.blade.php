@section('title', 'Survey Form')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style>
        .selectize-control {
            width: 300px !important;
        }

        .sno,.question,.options{
            padding: 25px;
            border: 1px solid #e7e7e7;
        }

        .option{
            padding-bottom: 10px;
        }
        .submit-btn{
            margin-top: 25px;
        }

        @media screen and (max-width:800px)
        {
            .sno , .form_header{
                display: none;
            }

            .question{
                font-weight: bold;
            }

            .content-wrapper{
                width: 100%;
                padding: 3% !important;
            }
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>

@endsection

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
    <head>
    @include('client.layout.header')
    </head>
    <body class="vertical-layout vertical-overlay-menu 2-columns menu-expanded fixed-navbar" data-open="click" data-menu="vertical-overlay-menu" data-col="2-columns">
        <nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-dark bg-primary navbar-shadow navbar-brand-center">
            <div class="navbar-wrapper">
            <div class="navbar-header" style="top: 0;">
                <ul class="nav navbar-nav flex-row">
                <li class="nav-item">
                    <a class="navbar-brand" href="{{route('cod.dashboard')}}">
                        <img class="brand-logo sonic" alt="Sonic" src="{{ asset('img/sonic_logo_white_new.png') }}">
                        <img class="brand-logo trax" alt="Trax" src="{{ asset('img/trax_logo_white_new.png') }}">
                    </a>
                </li>
                </ul>
            </div>
            </div>
        </nav>

        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-body">
                    <div class="app-content content">
                        <div class="content-wrapper">
                            <div class="content-header row">
                            </div>
                            <div class="content-body">
                                <h1 class="mb-1 text-center">
                                    Survey Form
                                </h1>

                                <div class="card">
                                    <div class="card-content" aria-expanded="true">
                                        <div class="card-body">
                                            @include('client.inc.messages')
                                            <form id="client_survey_form" class="form-inline mb-1 justify-content-center" method="post" action="{{route('survey.submit')}}">
                                                @csrf
                                                    {{-- survey_id --}}
                                                    <input type="hidden" name="survey_id" value="{{survey_id}}">
                                                    <div class="row text-left form_header" style="width: 100%">
                                                        <div class="col-sm-12 col-md-1 col-lg-1 align-middle sno">
                                                            <b> S.NO </b>
                                                        </div>
        
                                                        <div class="col-sm-12 col-md-6 col-lg-6 align-middle question">
                                                            <b> Questions </b>
                                                        </div>
        
                                                        <div class="col-sm-12 col-md-5 col-lg-5 options">
                                                            <b> Options </b>
                                                        </div>
                                                    </div>
                                                @foreach ($questions as $key =>  $question)                                                   
                                                    <div class="row text-left" style="width: 100%">
                                                        <div class="col-sm-12 col-md-1 col-lg-1 align-middle sno">
                                                            {{$key+1}}
                                                        </div>

                                                        <div class="col-sm-12 col-md-6 col-lg-6 align-middle question">
                                                            {{$question->questions}}
                                                        </div>

                                                        <div class="col-sm-12 col-md-5 col-lg-5 options">
                                                            <div class="option"> <input type="radio" name="option[{{$question->id}}]" class="form-select" value="{{$question->option1}}"> {{$question->option1}} </div>
                                                            <div class="option"> <input type="radio" name="option[{{$question->id}}]" class="form-select" value="{{$question->option2}}"> {{$question->option2}} </div>
                                                            <div class="option"> <input type="radio" name="option[{{$question->id}}]" class="form-select" value="{{$question->option3}}"> {{$question->option3}} </div>
                                                            <div class="option"> <input type="radio" name="option[{{$question->id}}]" class="form-select" value="{{$question->option4}}"> {{$question->option4}} </div>
                                                            <div class="option"> <input type="radio" name="option[{{$question->id}}]" class="form-select" value="" checked> None of the Above </div>
                                                            
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <div class="row submit-btn" style="width: 100%;">
                                                    <div class="col-sm-12 col-md-12 col-lg-12 text-center">
                                                        <input type="submit" value="Submit" name="submit" class="btn btn-primary">
                                                    </div>
                                                    
                                                </div>
                                                    
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @include('client.layout.footer')
    </body>
</html>

