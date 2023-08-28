<div>
    <span style="margin-bottom: 10px;">Dated: {{ $date }}</span>
    <div style="display: flex; flex-direction: column; border: 1px solid #ddd; margin-top: 0;">
        <div style="display: flex; background-color: #f2f2f2; padding: 8px; text-align: center; font-weight: bold;">
            <div style="flex: 1; border: 1px solid #ddd; padding: 0;">Sno</div>
            <div style="flex: 1; border: 1px solid #ddd; padding: 0;">Responsible Hub</div>
            <div style="flex: 1; border: 1px solid #ddd; padding: 0;">Total Tagged</div>
            <div style="flex: 1; border: 1px solid #ddd; padding: 0;">Response Rate (%)</div>
        </div>
        @php
            $sno = 1;
        @endphp
        @foreach ($responses as $resp)
        <div style="display: flex; padding: 0px; text-align: center;">
            <div style="flex: 1; border: 1px solid #ddd; padding: 0;">{{ $sno }}</div>
            <div style="flex: 1; border: 1px solid #ddd; padding: 0;">{{ $resp['responsible_hub'] }}</div>
            <div style="flex: 1; border: 1px solid #ddd; padding: 0;">{{ $resp['total_tagged'] }}</div>
            <div style="flex: 1; border: 1px solid #ddd; padding: 0;">{{ $resp['response_rate'] }}</div>
        </div>
        @php
            $sno++;
        @endphp
        @endforeach
    </div>
</div>


{{-- <div>
    <span style="margin-bottom: 10px;">Dated: {{ $date }}</span>
    <table style="border-collapse: collapse; width: 100%; border: 1px solid #ddd; margin-top: 0;">
        <thead>
            <tr>
                <th style="border: 1px solid #ddd; text-align: center; padding: 8px; background-color: #f2f2f2;">Sno</th>
                <th style="border: 1px solid #ddd; text-align: center; padding: 8px; background-color: #f2f2f2;">Responsible Hub</th>
                <th style="border: 1px solid #ddd; text-align: center; padding: 8px; background-color: #f2f2f2;">Total Tagged</th>
                <th style="border: 1px solid #ddd; text-align: center; padding: 8px; background-color: #f2f2f2;">Response Rate (%)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sno = 1;
            @endphp
            @foreach ($responses as $resp)
            <tr>
                <td style="border: 1px solid #ddd; text-align: center; padding: 8px;">{{ $sno }}</td>
                <td style="border: 1px solid #ddd; text-align: center; padding: 8px;">{{ $resp['responsible_hub'] }}</td>
                <td style="border: 1px solid #ddd; text-align: center; padding: 8px;">{{ $resp['total_tagged'] }}</td>
                <td style="border: 1px solid #ddd; text-align: center; padding: 8px;">{{ $resp['response_rate'] }}</td>
            </tr>
            @php
                $sno++;
            @endphp
            @endforeach
        </tbody>
    </table>
</div> --}}
