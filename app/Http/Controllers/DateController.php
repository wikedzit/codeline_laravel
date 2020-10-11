<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DateController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function countWeekends(Request $request) {
        $from = strtotime($request->from);
        $to   = strtotime($request->to);

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
