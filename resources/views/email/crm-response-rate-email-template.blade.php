<div>
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
</div>