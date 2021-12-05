<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use DatePeriod;

class DashboardController extends Controller
{
    public function index()
    {
        $startDate = Carbon::parse('first day of january')->toDateString(); // from 1/1/2021  
        $endDate = Carbon::parse('last day of December')->toDateString();  // to 31/12/2021
        $orders = Order::where('created_at', '>=', $startDate . '00:00:00')->where('created_at', '<=', $endDate . '23:59:59')->get()->toArray();
        $dataMonth = $this->getMonthListFromDate($startDate, $endDate);
        $arrPending = [];
        $arrDone = [];
        foreach ($dataMonth as $value) {
            foreach ($orders as $order) {
                $monthYear = Carbon::parse($order['created_at'])->format('y-m'); // 2021/1
                if ($monthYear == $value) {
                    if ($order['status'] == 1) {
                        $arrPending[$value] = isset($arrPending[$value]) ? $arrPending[$value] + 1 : 1;
                    } else {
                        $arrDone[$value] = isset($arrDone[$value]) ? $arrDone[$value] + 1 : 1;
                    }
                } else {
                    $arrPending[$value] = isset($arrPending[$value]) ? $arrPending[$value] : 0;
                    $arrDone[$value] = isset($arrDone[$value]) ? $arrDone[$value] : 0;
                }
            }
        }
        return view('admin.index', ['pending' => array_values($arrPending), 'done' => array_values($arrDone)]);
    }

    public function getMonthListFromDate($dateStart, $dateEnd)
    {
        $start = new DateTime($dateStart);
        $end = new DateTime($dateEnd);
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);

        $month = array();
        foreach ($period as $dt) {
            $month[] = $dt->format("y-m");
        }
        return $month;
    }
}
