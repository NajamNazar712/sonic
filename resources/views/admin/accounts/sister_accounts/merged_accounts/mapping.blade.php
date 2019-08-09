
@extends('admin.layout.master')
@section('title','Map Merged Accounts')

@section('content')
    <h1 class="mb-1">
        Map Merged Accounts
    </h1>
    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                <div class="justify-content-center">
                        <form class="form-horizontal text-center" action="{{route('admin.accounts.sister_account.merged_account.mapping.submit',['id'=>$id])}}" method="post" class="mt-2" id="editCityHubForm" novalidate="novalidate">
                            {{csrf_field()}}
                            <div class="justify-content-center width-60-per">
                                @foreach($merged_accounts as $account)
                                    <div class="row form-group">
                                        <div class="col">
                                            <h3 class="">{{ $account->company_name }}</h3>
                                        </div>
                                        @foreach($merged_accounts as $sub_account)
                                            @if($sub_account->id != $account->id)
                                                <div class="col text-right">
                                                    <fieldset class="checkbox-inline mr-1">
                                                        <input type="checkbox" id="sister_account_{{ $sub_account->id }}" class="icheck_square" name="sister_account[{{ $account->id }}][{{ $sub_account->id }}]">
                                                        <label for="sister_account_{{ $sub_account->id }}">{{$sub_account->company_name}}</label>
                                                    </fieldset>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <hr/>
                                @endforeach
                                    <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
        })
    </script>
@endsection