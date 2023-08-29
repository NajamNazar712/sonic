<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Ktown Rooms." />
    <title>Rate | Cases</title>

    <style>
        *{
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
            content: '★ ';
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
    </style>
</head>

<body>
    
    <form action="https://sonic.test/survey_form/email" method="POST">
    <table style="width:100%;">
        <thead>
            <tr>
                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Request #</th>
                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Type</th>
                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Resolution</th>
                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Status</th>
                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Resolved Within</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                @php
                    $resolved_within = $item['created_at']->diffInDays($item['updated_at']);
                    $type = \App\Http\Models\CRM\CrmRequestCaseNatureType::where('id', $item['case_nature_type_id'])->value('type');
                    
                    $resolution = \App\Http\Models\ShipmentsJourney::where('shipment_id', $item['shipment_id'])
                        ->latest()
                        ->first();
                    $resolution_reason = \App\Http\Models\ShipmentStatusReason::where('id', $resolution['shipper_status_id'])
                        ->latest()
                        ->first();
                @endphp
                <tr>
                    <td style="padding:5px; border: 1px solid black; border-collapse: collapse;">
                        <a href="{{ route('cod.crm.request.details', ['id' => $item['id']]) }}">
                            {{ $item['id'] }} (Click Here To Rate)
                        </a>
                    </td>
                    <td style="padding:5px; border: 1px solid black; border-collapse: collapse;">
                        {{ $type ?? '-' }}
                    </td>
                    <td style="padding:5px; border: 1px solid black; border-collapse: collapse;">
                        {{ $resolution_reason['name'] ?? '-' }}
                    </td>
                    <td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Closed</td>
                    <td style="padding:5px; border: 1px solid black; border-collapse: collapse;" class="rate">
                        {{ $resolved_within == 0 ? '1 Day' : $resolved_within . ' Days' }}
                    </td>
                    
                </tr>
            @endforeach
        </tbody>

        <button type="submit">Check</button>
    </table>
</form>
</body>

</html>
