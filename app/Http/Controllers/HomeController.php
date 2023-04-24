<?php

namespace App\Http\Controllers;

use App\Repositories\HolidayRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

/**
 * Controls the home/dashboard screen and it's functionality
 *
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * @var HolidayRepository
     */
    private $holidayRepository;


    /**
     * Create a new controller instance.
     *
     * @param HolidayRepository $holidayRepository
     */
    public function __construct(HolidayRepository $holidayRepository)
    {
        $this->middleware('auth');
        $this->holidayRepository = $holidayRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return Renderable
     */
    public function index(Request $request)
    {
        // loan summary data


        // holidays
        $targetDate = Carbon::now();
        $holidays = $this->_getHolidaysForCalendarByMonthYear($targetDate->month, $targetDate->year);
        return view('home');
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
}
