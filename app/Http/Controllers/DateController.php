<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DateController extends Controller
{
    /**
     * This method calculates the number weekends within a date range
     * To get a weekend day we first convert the start date and the end date and loop through the time stamps to find any that is of a weekend day.
     * To improve performance we skip all the days from Monday to Friday vy adding a correct number of seconds for that range
     * The skipping seconds are added each time we get Sunday
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function countWeekends(Request $request) {
        $from = strtotime($request->from);
        $to   = strtotime($request->to);

        //The value of @from has to be $smaller than that of $to
        if ($from > $to) {
            $tmp    = $to;
            $to     = $from;
            $from   = $tmp;
        }

        $time = $from;
        $count = 0;
        while($time <= $to) {
            $day = date('w', $time);
            //Check if day has the value of 0 or 6 ro represent Sunday and Saturday respectively
            //INcrease the counter if found
            if ($day == 0 || $day == 6) {
                $count+=1;
            }

            $daySeconds = 3600 * 24;
            $time += ($day == 0 )? $daySeconds * 5: $daySeconds;
        }

        try {

            $filename = ($request->has('filename'))? $request->filename: time().".txt";

            $text = sprintf("The number of weekends between %s and %s is %d", date('Y-m-d', $from), date('Y-m-d', $to), $count) ;
            Storage::put($filename, $text);
            return response()->json([
                'weekends' => $count,
                'file'     => $filename,
                'from'     => $from,
                'to'       => $to
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'An error has occurred'
            ], 500);
        }
    }
}
