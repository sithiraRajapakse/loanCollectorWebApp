<div class="card card-body">
    <div class="clearfix p-2 mb-2">
        <h4 class="float-left"><span id="hcv_monthp">{{ $targetDate->format('F') }}</span> - <span id="hcv_yearp">{{ $targetDate->format('Y') }}</span></h4>
        <div class="float-right">
            <form class="form-inline" id="calendar_month_selection_form" method="post" action="{{ route('settings.holidays.calendar.select-month') }}">
                @csrf
                <select name="hc_year" id="hc_year" class="form-control mb-2 mr-sm-2">
                    @for($y = 2020;$y < 2050;$y++)
                        <option value="{{ $y }}" {{ $targetDate->format('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <select name="hc_month" id="hc_month" class="form-control mb-2 mr-sm-2">
                    @foreach(monthsOfYear()  as $monthNumber => $monthName)
                        <option value="{{ $monthNumber }}" {{ $targetDate->format('m') == $monthNumber ? 'selected' : '' }}>{{ $monthName }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary mb-2">Select</button>
            </form>
        </div>
    </div>
    <div id="calendar-wrapper">
        <table id="calendar" class="table table-bordered">
            <colgroup>
                <col style="width: 14.286%;">
                <col style="width: 14.286%;">
                <col style="width: 14.286%;">
                <col style="width: 14.286%;">
                <col style="width: 14.286%;">
                <col style="width: 14.286%;">
                <col style="width: 14.286%;">
            </colgroup>
            <thead>
            <tr>
                <th>Monday</th>
                <th>Tuesday</th>
                <th>Wednesday</th>
                <th>Thursday</th>
                <th>Friday</th>
                <th>Saturday</th>
                <th>Sunday</th>
            </tr>
            </thead>
            <tbody>
            {!! $monthCalendar !!}
            </tbody>
        </table>
    </div>
</div>

@section('script-bottom')
    @parent
    <script>

    </script>
@stop
