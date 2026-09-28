<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\Notice;
use App\Models\faq;
use App\Models\req;

class ServiceController extends Controller
{
    //
    public function noticeView (Request $request) {
        $notices = Notice::orderBy('id', 'desc')->paginate(10);

        return view('service.notice', ['notices' => $notices]);
    }

    public function firstNoticeView(Request $request){
        $notices = Notice::orderBy('id', 'desc')->paginate(10);

        return view('service.notice', ['notices' => $notices])->withErrors(['error' => '여기는 시스템 관련 문의를 하는 곳입니다. 
        수업관련문의는 수업 교수자에게 해주시기를 부탁드립니다']);
    }

    public function noticeDetailView (Request $request) {
        $notice = Notice::find($request['noticeId']);
        if ($notice) {
            $page = $notice ? $notice->getPage() : Notice::count();

            Paginator::currentPageResolver(function () use($page) {
                return $page;
            });
        }
        $request->query->remove('noticeId');
        $notices = Notice::paginate(1);

        return view('service.notice_detail', ['notices' => $notices]);
    }
    public function faqView (Request $request) {
        $faqs = faq::orderBy('id', 'desc')->paginate(10);

        return view('service.faq', ['faqs' => $faqs]);
    }
    public function faqDetailView (Request $request) {
        $faq = faq::find($request['faqId']);
        if ($faq) {
            $page = $faq ? $faq->getPage() : faq::count();

            Paginator::currentPageResolver(function () use($page) {
                return $page;
            });
        }
        $request->query->remove('faqId');
        $faqs = faq::paginate(1);

        return view('service.faq_detail', ['faqs' => $faqs]);
    }
    public function reqView (Request $request) {
        $reqs = req::orderBy('id', 'desc')->where('userId', Auth::id())->paginate(10);

        return view('service.req', ['reqs' => $reqs]);
    }
    public function reqWriteView (Request $request) {
        if (!Auth::check()) {
            return redirect()->route('reqView')->withErrors(['service error' => '로그인이 필요한 서비스입니다.']);
        }


        return view('service.req_write');
    }
    public function reqWrite (Request $request) {
        if (!Auth::check()) {
            return redirect()->route('reqView')->withErrors(['service error' => '로그인이 필요한 서비스입니다.']);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('title')) {

            }
            else if ($errors->has('content')) {

            }

            return redirect()->back()->withErrors($errors)->withInput($request->input());
        }

        $req = req::create([
            'title' => $request['title'],
            'content' => $request['content'],
            'userId' => Auth::id(),
        ]);

        if (!$req) {
            return redirect()->back()->withInput($request->input());
        }
        else {
            return redirect()->route('reqView')->withErrors(['error' => '1:1 문의가 등록되었습니다.']);;
        }
    }
    public function reqDetailView (Request $request, $reqId) {
        $req = req::find($reqId);
        if (!$req) {
            return redirect()->route('reqView')->withErrors(['service error' => '문의가 삭제되었습니다.']);
        }

        return view('service.req_detail', ['req' => $req]);
    }
    public function reqEditView (Request $request, $reqId) {
        if (!Auth::check()) {
            return redirect()->route('reqDetailView', ['reqId' => $reqId])->withErrors(['service error' => '로그인이 필요한 서비스입니다.']);
        }
        
        $req = req::find($reqId);
        if (!$req) {
            return redirect()->route('reqView')->withErrors(['service error' => '문의가 삭제되었습니다.']);
        }
        
        if (Auth::user()->id != $req->userId) {
            return redirect()->route('reqDetailView', ['reqId' => $reqId])->withErrors(['service error' => '권한이 없습니다.']);
        }

        if (!is_null($req->adminAnswer)) {
            return redirect()->route('reqDetailView', ['reqId' => $reqId])->withErrors(['service error' => '답변이 완료된 문의입니다.']);
        }

        return view('service.req_edit', ['req' => $req]);
    }
    public function reqEdit (Request $request, $reqId) {
        if (!Auth::check()) {
            return redirect()->route('reqDetailView', ['reqId' => $reqId])->withErrors(['service error' => '로그인이 필요한 서비스입니다.']);
        }

        $req = req::find($reqId);
        if (!$req) {
            return redirect()->route('reqView')->withErrors(['service error' => '문의가 삭제되었습니다.']);
        }

        if (Auth::user()->id != $req->userId) {
            return redirect()->route('reqDetailView', ['reqId' => $reqId])->withErrors(['service error' => '권한이 없습니다.']);
        }

        if (!is_null($req->adminAnswer)) {
            return redirect()->route('reqDetailView', ['reqId' => $reqId])->withErrors(['service error' => '답변이 완료된 문의입니다.']);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('title')) {

            }
            else if ($errors->has('content')) {

            }

            return redirect()->back()->withErrors($errors)->withInput($request->input());
        }

        $req->title = $request['title'];
        $req->content = $request['content'];

        $req->save();

        return redirect()->route('reqDetailView', ['reqId' => $reqId]);
    }
    public function reqDelete (Request $request, $reqId) {
        if (!Auth::check()) {
            return redirect()->route('reqDetailView', ['reqId' => $reqId])->withErrors(['service error' => '로그인이 필요한 서비스입니다.']);
        }

        $req = req::find($reqId);
        if (!$req) {
            return redirect()->route('reqView')->withErrors(['service error' => '문의가 이미 삭제되었습니다.']);
        }

        //삭제는 관리자도 가능
        if (Auth::user()->id != $req->userId && Auth::user()->authority != 3) {
            return redirect()->route('reqDetailView', ['reqId' => $reqId])->withErrors(['service error' => '권한이 없습니다.']);
        }

        $req->delete();

        return redirect()->route('reqView');
    }

    public function noticeImageDownload (Request $request, $imagePathName) {
        $notice = Notice::where('imagePathName', $imagePathName)-> first();
        if (!$notice) {
            return null;
        }

        if (Storage::disk('local')->exists('notice/'.$imagePathName)) {
            $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
            $path = $storagePath.'notice/'.$imagePathName;

            return response()->download($path, $notice->imageName);
        }
        else {
//            $notice->imageName = null;
//            $notice->imagePathName = null;
//
//            $notice->save();
        }

        return null;
    }
}
