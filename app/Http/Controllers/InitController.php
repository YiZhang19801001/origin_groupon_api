<?php

namespace App\Http\Controllers;

use App\LayoutText;
use App\SalesGroup;
use Illuminate\Http\Request;

class InitController extends Controller
{
    public function index(Request $request)
    {
        $language_id = isset($request->language_id) ? $request->language_id : 2;
        $custom_setting = array();

        $custom_setting = \Config::get('custom');

        $layout_text = LayoutText::all();
        $labels = array();
        foreach ($layout_text as $item) {

            $desc = $item->descriptions()->where("language_id", $language_id)->first();
            if ($desc === null) {
                $desc = $item->descriptions()->first();
            }

            $labels[$item->name] = $desc->text;
        }

        # create app_status
        // today
        $dt = new \DateTime("now", new \DateTimeZone('Australia/Sydney'));
        $today = $dt->format('Y-m-d');
        // if any sales group only now
        $sales_group = SalesGroup::where("start_date", "<=", $today)
            ->where("end_date", ">=", $today)
            ->first();
        if ($sales_group !== null) { // yes, return information of opened sales group
            $isOpen = true;
            $start_date = $sales_group->start_date;
            $end_date = $sales_group->end_date;
        } else {
            $isOpen = false; // no, try to find closest open date for next sales group
            $sales_group = SalesGroup::where("start_date", ">=", $today)->first();
            if ($sales_group !== null) {
                $start_date = $sales_group->start_date;
                $end_date = $sales_group->end_date;
            } else { // not found any sales group in future, return empty string, client side will render some text message to user
                $start_date = "";
                $end_date = "";
            }
        }

        $app_status = array(
            "isOpen" => $isOpen,
            "start_date" => $start_date,
            "end_date" => $end_date,
        );
        return response()->json(compact("custom_setting", "labels", "app_status"), 200);
    }
}
