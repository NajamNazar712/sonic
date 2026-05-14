@extends('admin.layout.master')

@section('title', 'T-Payment Cycle')

@section('content')
    @if(!empty($user))
        <section>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="font-large-1">
                                {{ $user->name }}
                                <!-- <div class="badge badge-success pull-right">Active Account</div> -->
                            </h2>
                            @include('admin.inc.messages')
                        </div>

                        <div class="card-content">
                            <form id="paymentCycleForm"
                                  class="card-body card-dashboard"
                                  action="{{ route('admin.accounts.t_payments.store', ['id' => $user->id]) }}"
                                  method="post"
                                  novalidate="novalidate">
                                @csrf
                                @method('PUT')

                                <div class="card border-success">
                                    <div class="card-header border-success">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3 class="display-inline card-title lead success">T-Payment Cycle</h3>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="value">Select T-Payment Cycle</label>
                                                    <fieldset class="form-group">
                                                        @php
                                                            $selectedValue = old('value');

                                                            if ($selectedValue === null) {
                                                                $selectedValue = optional($pendingPaymentCycle)->value ?? optional($paymentCycle)->value;
                                                            }

                                                            $currentValue = optional($paymentCycle)->value;
                                                            $pendingValue = optional($pendingPaymentCycle)->value;

                                                            $inputClass = 'form-control';
                                                            if ($pendingPaymentCycle && $currentValue && $pendingValue !== $currentValue) {
                                                                $inputClass .= ' changed';
                                                            } elseif ($pendingPaymentCycle && !$currentValue) {
                                                                $inputClass .= ' new';
                                                            }
                                                        @endphp

                                                        <select name="value"
                                                                id="value"
                                                                class="{{ $inputClass }}"
                                                                required>
                                                            <option value="">Select T-Payment Cycle</option>
                                                            <option value="T-0" {{ $selectedValue == 'T-0' ? 'selected' : '' }}>T-0</option>
                                                            <option value="T-1" {{ $selectedValue == 'T-1' ? 'selected' : '' }}>T-1</option>
                                                            <option value="T-2" {{ $selectedValue == 'T-2' ? 'selected' : '' }}>T-2</option>
                                                            <option value="T-3" {{ $selectedValue == 'T-3' ? 'selected' : '' }}>T-3</option>
                                                            <option value="T-4" {{ $selectedValue == 'T-4' ? 'selected' : '' }}>T-4</option>
                                                            <option value="T-5" {{ $selectedValue == 'T-5' ? 'selected' : '' }}>T-5</option>
                                                        </select>

                                                        @error('value')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                               <div class="col-md-5">
                                                    <div class="card border-primary">
                                                        <div class="card-header pb-0">
                                                            <h4 class="card-title">Current Approved Value</h4>
                                                        </div>
                                                        <div class="card-body">
                                                            <h5>{{ $paymentCycle->value ?? 'Not set yet' }}</h5>

                                                            @if($paymentCycle)
                                                                <small class="d-block text-muted">
                                                                    Added By:
                                                                    {{ optional($paymentCycle->addedByUser)->name ?? 'N/A' }}
                                                                </small>
                                                                <small class="d-block text-muted">
                                                                    Added At:
                                                                    {{ optional($paymentCycle->added_at)->format('Y-m-d h:i A') }}
                                                                </small>
                                                                <small class="d-block text-muted">
                                                                    Approved By:
                                                                    {{ optional($paymentCycle->approvedByUser)->name ?? 'N/A' }}
                                                                </small>
                                                                <small class="d-block text-muted">
                                                                    Approved At:
                                                                    {{ optional($paymentCycle->approved_at)->format('Y-m-d h:i A') }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-5">
                                                    <div class="card border-warning">
                                                        <div class="card-header pb-0">
                                                            <h4 class="card-title">Pending Value</h4>
                                                        </div>
                                                        <div class="card-body">
                                                            <h5>{{ $pendingPaymentCycle->value ?? 'No pending request' }}</h5>

                                                            @if($pendingPaymentCycle)
                                                                <small class="d-block text-muted">
                                                                    Added By:
                                                                    {{ optional($pendingPaymentCycle->addedByUser)->name ?? 'N/A' }}
                                                                </small>
                                                                <small class="d-block text-muted">
                                                                    Added At:
                                                                    {{ optional($pendingPaymentCycle->added_at)->format('Y-m-d h:i A') }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-md-12">
                                                    <button type="submit" class="btn btn-success">
                                                        Submit for Approval
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            @if($pendingPaymentCycle && (session('role_id') == 1 || in_array(8, session('permissions', []))))
                                <div class="card-body">
                                    <div class="card mt-2 border-warning">
                                        <div class="card-header border-warning">
                                            <h3 class="card-title">Approve Pending T-Payment Cycle</h3>
                                        </div>
                                        <div class="card-body">
                                            <!-- <p class="mb-1">
                                                <strong>Current:</strong> {{ $paymentCycle->value ?? 'Not set yet' }}
                                            </p> -->

                                            <!-- @if($paymentCycle)
                                                <p class="mb-1">
                                                    <strong>Current Added By:</strong>
                                                    {{ optional($paymentCycle->addedByUser)->name ?? 'N/A' }}
                                                </p>
                                                <p class="mb-1">
                                                    <strong>Current Added At:</strong>
                                                    {{ optional($paymentCycle->added_at)->format('Y-m-d h:i A') }}
                                                </p>
                                                <p class="mb-1">
                                                    <strong>Current Approved By:</strong>
                                                    {{ optional($paymentCycle->approvedByUser)->name ?? 'N/A' }}
                                                </p>
                                                <p class="mb-2">
                                                    <strong>Current Approved At:</strong>
                                                    {{ optional($paymentCycle->approved_at)->format('Y-m-d h:i A') }}
                                                </p>
                                            @endif -->

                                            <p class="mb-1">
                                                <strong>Pending:</strong> {{ $pendingPaymentCycle->value }}
                                            </p>
                                            <p class="mb-1">
                                                <strong>Pending Added By:</strong>
                                                {{ optional($pendingPaymentCycle->addedByUser)->name ?? 'N/A' }}
                                            </p>
                                            <p class="mb-2">
                                                <strong>Pending Added At:</strong>
                                                {{ optional($pendingPaymentCycle->added_at)->format('Y-m-d h:i A') }}
                                            </p>

                                            <form action="{{ route('admin.accounts.t_payments.approve', ['id' => $user->id]) }}"
                                                method="post"
                                                >
                                                @csrf
                                                <button type="submit" class="btn btn-primary">
                                                    Approve
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        <h1>User does not exist.</h1>
    @endif
@endsection

@section('css')
    <style type="text/css">
        .changed {
            background-color: #F7F087 !important;
        }

        .new {
            background-color: #78FF67 !important;
        }
    </style>
@endsection