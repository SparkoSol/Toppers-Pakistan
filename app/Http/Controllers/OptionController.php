<?php

namespace App\Http\Controllers;

use App\Option;
use App\OptionValue;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function getByItem($id) {
        // return Option::where('item_id', $id)->get();
        $options = Option::where('item_id', $id)->get();
        $sendOption = [];
        foreach ($options as $option) {
            $values = OptionValue::where('option_id', $option->id)->get();
            $optionValue = '';
            for ($i = 0; $i < count($values); $i++) {
                if ($i == (count($values) - 1)) {
                    $optionValue .= $values[$i]->name;
                } else {
                    $optionValue .= $values[$i]->name .',';
                }
            }
            $optionApi = new OptionApi();
            $optionApi->name = $option->name;
            $optionApi->values = $optionValue;
            array_push($sendOption,$optionApi);
        }
        return $sendOption;
    }
    public function getByItemApp($id) {
        return Option::where('item_id',$id)->with('values')->get();
    }
}

class OptionApi {
    public $name;
    public $values;
}
