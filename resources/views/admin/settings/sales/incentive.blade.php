@extends('admin.layout.master')

@section('title', 'Sales Incentive Settings')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                   Sales Incentive Settings
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form method="post" id="int_rates_settings" class="form-horizontal text-center" novalidate="novalidate">
                                {{ csrf_field() }}
                                <div class="row justify-content-center">
                                    <div class="col-3 form-group">
                                        <label><strong>Class A</strong></label>
                                        <div class="input-group">
                                            <input type="text" name="classA" class="form-control decimal" placeholder="Class A*" value="100000">
                                            <div class="input-group-append">
                                                <span class="input-group-text">Rs</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-3 form-group">
                                        <label><strong>Class B</strong></label>
                                        <div class="input-group">
                                            <input type="text" name="classB" class="form-control decimal" placeholder="Class B*" value="100000">
                                            <div class="input-group-append">
                                                <span class="input-group-text">Rs</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-3 form-group">
                                        <label><strong>Class C</strong></label>
                                        <div class="input-group">
                                            <input type="text" name="classC" class="form-control decimal" placeholder="Class C*" value="100000">
                                            <div class="input-group-append">
                                                <span class="input-group-text">Rs</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-3 form-group">
                                        <label><strong>Class D</strong></label>
                                        <div class="input-group">
                                            <input type="text" name="classD" class="form-control decimal" placeholder="Class D*" value="3000">
                                            <div class="input-group-append">
                                                <span class="input-group-text">Rs</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row justify-content-center">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection