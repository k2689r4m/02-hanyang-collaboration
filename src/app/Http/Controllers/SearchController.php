<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\MyClass;

class SearchController extends Controller
{
    //
    public function searchView (Request $request) {
        $validator = Validator::make($request->all(), [
            'search' => ['string', 'nullable', 'min:2'],
        ]);

        $search = $request['search'];

        $myClasses = MyClass::with(['withUser' => function ($query) {
            $query->select('id', 'name');
        }])->with(['withClassApply' => function ($query) {
            $query->select('id', 'korName', 'classObjectId');
        }])->get()->filter(function ($item) use($search) {
            return is_null($search) || $search == '' ? true : str_contains($item->withUser->name, $search) || str_contains($item->withClassApply->korName, $search);
        });

//        return dd($myClasses[0]->classLists());

        return view('search.search', ['myClasses' => $myClasses]);
    }
}
