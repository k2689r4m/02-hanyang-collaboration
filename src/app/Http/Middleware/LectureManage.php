<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MyClass;
use App\Models\ClassApply;
use App\Models\Team;
use App\Models\Expert;
use App\Models\ClassManager;

class LectureManage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $COMMON_USER = 1;
        $PROFESSOR = 2;
        $EXTERNAL_EXPERT = 5;
        $MANAGER = 6;

        if (Auth::check()) {
            if (Auth::user()->authority == $PROFESSOR) {
                if ($request->route('classObjectId')) {
                    $myClass = MyClass::where('classObjectId', $request->route('classObjectId'))->first();
                    if ($myClass) {
                        if (Auth::id() == $myClass->userId) {
//                            $teams = Team::where('classObjectId', $request->route('classObjectId'))->get();
//
//                            if ($teams->count() < 20) {
//                                foreach (range($teams->count() + 1, 20) as $i) {
//                                    Team::create([
//                                        'name' => '팀'.$i,
//                                        'classObjectId' => $request->route('classObjectId'),
//                                    ]);
//                                }
//                            }

                            return $next($request);
                        }
                    }
                }
                else if ($request->route('classApplyId')) {
                    $classApply = ClassApply::find($request->route('classApplyId'));
                    if ($classApply) {
                        if ($classApply->item()->card()->userId == Auth::id()) {
                            return $next($request);
                        }
                    }
                }
                else {
                    return $next($request);
                }
            }
            else if (Auth::user()->authority == $EXTERNAL_EXPERT) {
                if ($request->route('classObjectId')) {
                    $expert = Expert::where('classObjectId', $request->route('classObjectId'))->where('userId', Auth::id())->first();
                    if ($expert) {
                        return $next($request);
                    }
                }
                else if ($request->route('classApplyId')) {
                    $classApply = ClassApply::find($request->route('classApplyId'));
                    if ($classApply) {
                        if ($classApply != 'wait') {
                            $expert = Expert::where('classObjectId', $classApply->classObjectId)->where('userId', Auth::id())->first();
                            if ($expert) {
                                return $next($request);
                            }
                        }
                    }
                }
                else {
                    return $next($request);
                }
            }
            else if (Auth::user()->authority == $MANAGER) {
                if ($request->route('classObjectId')) {
                    $manager = ClassManager::where('classObjectId', $request->route('classObjectId'))->where('userId', Auth::id())->first();
                    if ($manager) {
                        return $next($request);
                    }
                }
                else if ($request->route('classApplyId')) {
                    $classApply = ClassApply::find($request->route('classApplyId'));
                    if ($classApply) {
                        if ($classApply != 'wait') {
                            $manager = ClassManager::where('classObjectId', $classApply->classObjectId)->where('userId', Auth::id())->first();
                            if ($manager) {
                                return $next($request);
                            }
                        }
                    }
                }
                else {
                    return $next($request);
                }
            }
        }
        return redirect('/myPage');
    }
}
