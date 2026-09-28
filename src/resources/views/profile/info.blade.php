@extends('layouts.layout')

@section('content')
    <div class="container">
        <div class="container__wrap">
            <div class="contents__wrap p-40 w-800">
                <h3 class="con-tit">내 정보</h3>
                <div class="info-img__wrap">
                    <img id="avatar-preview" src="{{ route('avatar', ['userId' => auth()->id()]) }}">
                </div>
{{--                <div class="sns-login__state">--}}
{{--                    <div class="kakao">카카오톡 로그인</div>--}}
{{--                    <div class="naver">네이버 로그인</div>--}}
{{--                    <div class="hy">한양대 SSO 로그인</div>--}}
{{--                </div>--}}
                <div class="col-wrap">
                    <div class="input-wrap col-6">
                        <label class="input-label">아이디</label>
                        <div class="input guide-exist">
                            <input type="text" value="{{ auth()->user()->email }}" disabled />
                        </div>
                        <label class="input-label">이름*</label>
                        <div class="input guide-exist">
                            <input type="text" value="{{ auth()->user()->name }}" disabled />
                        </div>
                    </div>
                    <div class="input-wrap col-6">
                        <label class="input-label">휴대폰번호*</label>
                        <div class="input guide-exist">
                            <input type="text" value="{{ auth()->user()->contact }}" disabled />
                        </div>
                        <label class="input-label">코드</label>
                        <div class="input guide-exist">
                            <input type="text" value="{{ auth()->user()->code }}" disabled />
                        </div>
                    </div>
                </div>
{{--                @if(auth()->user()->social === null)--}}
                <div class="t-center">
                    <button class="btn btn-md2 btn-primary" onclick="location.href='{{ route('profileEditView') }}'">수정</button>
                </div>
            </div>
        </div>
    </div>
@endsection

