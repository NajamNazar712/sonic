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
                                        <label><strong>BDM</strong></label>
                                        <div class="input-group">
                                            <input type="text" name="BDM" class="form-control decimal" placeholder="BDM*" value="100000">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-3 form-group">
                                        <label><strong>TM</strong></label>
                                        <div class="input-group">
                                            <input type="text" name="TM" class="form-control decimal" placeholder="TM*" value="100000">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-3 form-group">
                                        <label><strong>AM</strong></label>
                                        <div class="input-group">
                                            <input type="text" name="ZM" class="form-control decimal" placeholder="AM*" value="100000">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-3 form-group">
                                        <label><strong>ZM</strong></label>
                                        <div class="input-group">
                                            <input type="text" name="ZM" class="form-control decimal" placeholder="ZM*" value="3000">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
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