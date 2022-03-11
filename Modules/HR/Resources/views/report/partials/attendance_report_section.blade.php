@if (!empty($period))
<div class="print-attendance">
    <table class="table table-bordered table-responsive">
        <thead>
            <tr>
                <th class="active" colspan="32">{{$employee->first_name}} {{$employee->last_name}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($period as $item)
            <tr>
                @php $date = $item->format("Y-m") @endphp
                <td rowspan="2" valign="middle"><strong>{{$date}}</strong></td>

                @foreach ($dateSl[$date] as $item)
                <td>{{$item }}</td>
                @endforeach
            </tr>
            <tr>
                @foreach ($attendance[$date] as $atten)
                <td>
                    @if ($atten->attendance_status == '1')
                    <small class="label bg-olive">P</small>
                    
                    @elseif ($atten->attendance_status == '0')
                    <small class="label bg-red">A</small>
                   
                    @elseif($atten->attendance_status == '3')
                    <small class="label bg-yellow">L</small>
                  
                    @elseif ($atten->attendance_status == 'H')
                    <small class="label btn-default">H</small>
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif