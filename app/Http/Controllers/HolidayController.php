<?php

namespace App\Http\Controllers;

use App\Repositories\HolidayRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class HolidayController extends Controller
{
    /**
     * @var HolidayRepository
     */
    private $holidayRepository;

    /**
     * HolidayController constructor.
     * @param HolidayRepository $holidayRepository
     */
    public function __construct(HolidayRepository $holidayRepository)
    {
        $this->middleware('auth');
        $this->holidayRepository = $holidayRepository;
    }

    /**
     * Get holidays list for the given calendar month and year
     *
     * @param $month
     * @param $year
     * @return array
     */
    private function _getHolidaysForCalendarByMonthYear($month, $year): array
    {
        $_holidays = $this->holidayRepository->byMonthYear($month, $year);
        $_h = [];
        foreach ($_holidays as $_holiday) {
            $_date = Carbon::parse($_holiday->date);
            $_h[$_date->day] = [
                'id' => $_holiday->id,
                'title' => $_holiday->title,
                'description' => $_holiday->description,
            ];
        }
        return $_h;
    }

    /**
     * Show calendar page
     * @return Application|Factory|View
     */
    public function index()
    {
        $targetDate = Carbon::now();

        if (session('hc_month') && session('hc_year')) {
            $targetDate = Carbon::create(session('hc_year'), session('hc_month'));
        } else {
            Session::put('hc_month', $targetDate->month);
            Session::put('hc_year', $targetDate->year);
        }

        $holidays = $this->_getHolidaysForCalendarByMonthYear($targetDate->month, $targetDate->year);
        $monthCalendar = generate_calendar_html($targetDate->month, $targetDate->year, $holidays);

        return view('settings.holidays', compact('monthCalendar', 'targetDate'));
    }

    /**
     * Create holiday entry
     * @param Request $request
     * @return RedirectResponse
     */
    public function create(Request $request): RedirectResponse
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'date' => 'required|date',
            'title' => 'required',
            'description' => 'nullable'
        ]);
        if ($validator->fails()) {
            return redirect()->route('settings.holidays')->withErrors($validator)->withInput()->with('error', 'Cannot create the holiday entry. You have some errors in your input.');
        }

        $this->holidayRepository->save([
            'date' => $data['date'],
            'title' => $data['title'],
            'description' => $data['description']
        ]);

        return redirect()->route('settings.holidays')->with('success', 'Holiday created successfully');
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function edit(Request $request, $id)
    {
        $holiday = $this->holidayRepository->find($id);
        if (empty($holiday)) {
            return redirect()->route('settings.holidays')->with('error', 'Requested holiday entry not found.');
        }

        $targetDate = Carbon::parse($holiday->date);

        // show the month of the selected date in the calendar automatically.
        // not showing the session even if there are session vars for hc_year and hc_month.
        // this is temporary.
        $holidays = $this->_getHolidaysForCalendarByMonthYear($targetDate->month, $targetDate->year);
        $monthCalendar = generate_calendar_html($targetDate->month, $targetDate->year, $holidays);

        return view('settings.holidays-edit', compact('monthCalendar', 'holiday' ));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $holiday = $this->holidayRepository->find($id);
        if (empty($holiday)) {
            return redirect()->route('settings.holidays')->with('error', 'Requested holiday entry not found.');
        }

        $data = $request->post();

        $validator = Validator::make($data, [
            'date' => 'required|date',
            'title' => 'required',
            'description' => 'nullable'
        ]);
        if ($validator->fails()) {
            return redirect()->route('settings.holidays')->withErrors($validator)->withInput()->with('error', 'Cannot create the holiday entry. You have some errors in your input.');
        }

        $this->holidayRepository->update($holiday->id, [
            'date' => $data['date'],
            'title' => $data['title'],
            'description' => $data['description']
        ]);

        return redirect()->route('settings.holidays')->with('success', 'Holiday entry updated successfully');
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function delete(Request $request, $id): RedirectResponse
    {
        $holiday = $this->holidayRepository->find($id);
        if (empty($holiday)) {
            return redirect()->route('settings.holidays')->with('error', 'Requested holiday entry not found.');
        }

        $this->holidayRepository->delete($id);

        return redirect()->route('settings.holidays')->with('success', 'Holiday entry moved to trash successfully.');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function selectCalendarMonth(Request $request): RedirectResponse
    {
        $hc_year = $request->post('hc_year');
        $hc_month = $request->post('hc_month');

        Session::put("hc_year", $hc_year);
        Session::put("hc_month", $hc_month);

        // redirect to index route
        return redirect()->route('settings.holidays');
    }

}
