<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    private static $cAuthPageList = [
        'admin.class1View',
        'admin.class2View',
        'admin.class3View',
        'admin.class3ProfessorView',
        'admin.class4View',
        'admin.classStatisticsView',
        'admin.classCertificateView',
        'admin.classDetail1View',
        'admin.classDetail2View',
        'admin.classDetail3View',
        'admin.classDetail4View',
        'admin.classDetail4DetailView',
        'admin.classDetail5View',
        'admin.classDetail5DetailView',
        'admin.classDetail6View',
        'admin.classDetail7View',
        'admin.classDetail7DetailView',
        'admin.classDetail8View',
        'admin.pwdCheckView',
        'admin.pwdCheck',
        'admin.pwdEdit',
    ];
    private static $authBlackList = [
        'admin.pwdCheckView',
        'admin.pwdEditView',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (Auth::user()->authority == 3) {
                if (in_array(($request->route()->action['as'] ?? false), Admin::$authBlackList)) {
                    return redirect()->back();
                }
                return $next($request);
            }
            if (Auth::user()->authority) {
                if (in_array(($request->route()->action['as'] ?? false), Admin::$cAuthPageList)) {
                    $request->merge(['gnjSosokNm' => Auth::user()->daehakNm]);
                    return $next($request);
                }
//                elseif(Auth::user()->authority == 9) {
//                    return redirect()->route('admin.memberView');
//                }
                else{
//                    return redirect()->route('admin.memberView');
                    return redirect()->route('admin.class1View');
                }
            }
            else {
                Auth::logout();
            }
        }

        return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
    }
}
