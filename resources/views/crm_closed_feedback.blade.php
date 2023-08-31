<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    @include('client.layout.header')
</head>

<body class="vertical-layout vertical-overlay-menu 2-columns menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-overlay-menu" data-col="2-columns">
    <nav
        class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-dark bg-primary navbar-shadow navbar-brand-center">
        <div class="navbar-wrapper">
            <div class="navbar-header" style="top: 0;">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item">
                        <a class="navbar-brand" href="{{ route('cod.dashboard') }}">
                            <img class="brand-logo sonic" alt="Sonic"
                                src="{{ asset('img/sonic_logo_white_new.png') }}">
                            <img class="brand-logo trax" alt="Trax"
                                src="{{ asset('img/trax_logo_white_new.png') }}">
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        th {
            color: white;
            background-color: #649bc8;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        * {
            margin: 0;
            padding: 0;
        }

        .rate {
            float: left;
            height: 46px;
            padding: 0 10px;
        }

        .rate:not(:checked)>input {
            position: absolute;
            top: -9999px;
        }

        .rate:not(:checked)>label {
            float: right;
            width: 1em;
            overflow: hidden;
            white-space: nowrap;
            cursor: pointer;
            font-size: 30px;
            color: #ccc;
        }

        .rate:not(:checked)>label:before {
            content: "★";
        }

        .rate>input:checked~label {
            color: #ffc700;
        }

        .rate:not(:checked)>label:hover,
        .rate:not(:checked)>label:hover~label {
            color: #deb217;
        }

        .rate>input:checked+label:hover,
        .rate>input:checked+label:hover~label,
        .rate>input:checked~label:hover,
        .rate>input:checked~label:hover~label,
        .rate>label:hover~input:checked~label {
            color: #c59b08;
        }

        .color{
            color: #64a0d2 
        }
    </style>

    <div class="content-body">

        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row">
                </div>
                <div class="content-body">
                    @if ($crm_closed_cases->isNotEmpty())

                    <h1 class="mb-1">
                        Crm Closed Cases
                    </h1>
                    <form action="{{ route('survey.feedback.submit') }}" method="post">
                        @csrf
                        <div class="card">
                            <div class="card-content" aria-expanded="true">
                                <div class="card-body">
                                    @include('client.inc.messages')
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tr>
                                                <th>Request Number</th>
                                                <th>Type</th>
                                                <th>Resolution</th>
                                                <th>Status</th>
                                                <th>Resolved WithIn</th>
                                                <th>Rate</th>

                                            </tr>
                                            @foreach ($crm_closed_cases as $crm_closed_case)
                                                        @php
                                                            $type = App\Http\Models\CRM\ CrmRequestCaseNatureType::where('id', $crm_closed_case['case_nature_type_id'])->value('type');
                                                            $resolved_within = $crm_closed_case['created_at']->diffInDays($crm_closed_case['updated_at']);
                                                            $resolution = App\Http\Models\ShipmentsJourney::where('shipment_id', $crm_closed_case['shipment_id'])->latest()->first();
                                                            $resolution = App\Http\Models\ShipmentStatusReason::where('id', $resolution["shipper_status_id"])->latest()->first();
                                                        @endphp
                                                <tr>
                                                    <td style="width: 300px">{{ $crm_closed_case->id ?? '-' }}</td>
                                                    
                                                    <td style="width: 300px">
                                                        {{ $type ?? '-' }}
                                                    </td>
                                                    
                                                    <td style="width: 300px">
                                                        {{ $resolution['name'] ?? '-' }}
                                                    </td>
                                                    
                                                    <td style="widxth: 300px">Closed</td>
                                                    
                                                    <td style="width: 300px">
                                                        {{ $resolved_within == 0 ? '1 Day' : $resolved_within . ' Days' }}
                                                    </td>

                                                    <td style="width: 300px">
                                                        <div class="rate">
                                                            @for ($i = 5; $i >= 1; $i--)
                                                                <?php
                                                                $inputName = $crm_closed_case->id;
                                                                $inputId = 'star_' . $crm_closed_case->id . '-' . $i;
                                                                ?>
                                                                <input type="radio" class="star-{{ $i }}"
                                                                    id="{{ $inputId }}" name="{{ $inputName }}"
                                                                    value="{{ $i }}" />
                                                                <label class="star-{{ $i }}"
                                                                    for="{{ $inputId }}"
                                                                    title="{{ $i }} star"></label>
                                                            @endfor
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>

                                        <div class="text-center"> <!-- Center-align content -->
                                            <button class="btn btn-info">Submit Your Response</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    @else


                    <div class="text-center mt-4">
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-check-circle"></i> Your Response Has Been Successfully Submitted
                        </div>
                    </div>
                    


                    @endif
                </div>
            </div>
        </div>
    </div>

</body>

</html>
