<?php
use Carbon\Carbon;

if (!function_exists('generate_calendar_html')) {
    /**
     * @param $month
     * @param $year
     * @param $data
     * @return string
     */
    function generate_calendar_html($month = null, $year = null, $data = []): string
    {
        $now = Carbon::now();
        if (empty($month)) {
            $month = $now->month;
        }
        if (empty($year)) {
            $year = $now->year;
        }

        $templates = [
            'day_of_month' => '<td><a href="#"></a>%d</td>',
            'holiday' => '<td class="holiday"><a href="/settings/holidays/%s"><span class="day-num">%d</span><br/>%s</a></td>',
            'weekStart' => '<tr>',
            'weekEnd' => '</tr>',
            'regularDay' => '<td></td>',
        ];

        $monthStart = Carbon::create($year, $month);
        $startOfMonth = $monthStart->startOfMonth();

        $monthEnd = Carbon::create($year, $month);
        $endOfMonth = $monthEnd->endOfMonth();

        $period = Carbon::parse($startOfMonth->format('Y-m-d'))->daysUntil($endOfMonth->format('Y-m-d'));
        $output = null;
        $week = 1;
        foreach ($period as $key => $date) {
            $currentDate = $date->format('Y-m-d');
            $dayOfMonth = $date->day;
            $weekofMonth = $date->weekOfMonth;
            $weekofYear = $date->weekOfYear;
            $dayOfWeek = $date->dayOfWeek;

            $output[$week][$date->dayName] = [
                'date' => $currentDate,
                'dayName' => $date->dayName,
                'innerHTML' => array_key_exists($dayOfMonth, $data) ? sprintf($templates['holiday'], $data[$dayOfMonth]['id'], $dayOfMonth, $data[$dayOfMonth]['title']) : sprintf($templates['day_of_month'], $dayOfMonth),
            ];
            if ($date->dayName == 'Sunday') {
                $week++;
            }
        }

        $daysOfWeek = daysOfWeek();

        $_markup = '';
        foreach ($output as $week) {
            $_markup .= $templates['weekStart'];

            foreach ($daysOfWeek as $weekDay) {
                if (array_key_exists($weekDay, $week)) {
                    $_markup .= $week[$weekDay]['innerHTML'];
                } else {
                    $_markup .= $templates['regularDay'];
                }
            }

            $_markup .= $templates['weekEnd'];
        }

       return $_markup;
    }
}

if (!function_exists('daysOfWeek')) {
    /**
     * @return string[]
     */
    function daysOfWeek(): array
    {
        return ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',];
    }
}

if (!function_exists('monthsOfYear')) {
    /**
     * @return string[]
     */
    function monthsOfYear(): array
    {
        return [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];
    }
}

if (!function_exists('ucfirst_forced')) {
    /**
     * Better UCFirst function to handle all case text
     * @param $str String to be transformed
     * @return string
     */
    function ucfirst_forced($str): string
    {
        $_lcstr = strtolower($str);
        return ucfirst($_lcstr);
    }
}
