@extends('layouts.layout')

@section('content')
<div class="container">
    <div class="login-wrap">
        <div class="login">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="login-input id">
                    <input type="text" name="email" placeholder="아이디를 입력해주세요." class="login-input__id" />
                </div>
                <div class="login-input pwd">
                    <input type="password" name="password" placeholder="비밀번호를 입력해주세요." class="login-input__pwd" autocomplete="off" />
                </div>
                <div class="find-pwd">
                    <span class="fc-gray">비밀번호를 잊으셨나요?</span>
                    <a class="btn" href="{{ route('password.request') }}">비밀번호 찾기</a>
                </div>
                <button class="btn btn-primary btn-full">로그인</button>
                <ul class="sns-list">
                    <li class="sns-list__item kakao"><a href="javascript:;">카카오<br />로그인</a></li>
                    <li class="sns-list__item naver"><a href="javascript:;">네이버<br />로그인</a></li>
                    <li class="sns-list__item hy" onclick="location.href=`{{ route('auth.hy.login') }}`"><a href="javascript:;">한양대<br />SSO</a></li>

                </ul>
                <p class="fc-gray t-center p-10">아직 회원이 아니신가요?</p>
                <button type="button" class="btn btn-white btn-shadow btn-full" onclick="location.href='{{ route('register') }}'">회원가입</button>
            </form>
        </div>
    </div>
{{--    <div class="row justify-content-center">--}}
{{--        <div class="col-md-8">--}}
{{--            <div class="card">--}}
{{--                <div class="card-header">{{ __('Login') }}</div>--}}

{{--                <div class="card-body">--}}
{{--                    <form method="POST" action="{{ route('login') }}">--}}
{{--                        @csrf--}}

{{--                        <div class="form-group row">--}}
{{--                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>--}}

{{--                            <div class="col-md-6">--}}
{{--                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>--}}

{{--                                @error('email')--}}
{{--                                    <span class="invalid-feedback" role="alert">--}}
{{--                                        <strong>{{ $message }}</strong>--}}
{{--                                    </span>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="form-group row">--}}
{{--                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>--}}

{{--                            <div class="col-md-6">--}}
{{--                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">--}}

{{--                                @error('password')--}}
{{--                                    <span class="invalid-feedback" role="alert">--}}
{{--                                        <strong>{{ $message }}</strong>--}}
{{--                                    </span>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="form-group row">--}}
{{--                            <div class="col-md-6 offset-md-4">--}}
{{--                                <div class="form-check">--}}
{{--                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>--}}

{{--                                    <label class="form-check-label" for="remember">--}}
{{--                                        {{ __('Remember Me') }}--}}
{{--                                    </label>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="form-group row mb-0">--}}
{{--                            <div class="col-md-8 offset-md-4">--}}
{{--                                <button type="submit" class="btn btn-primary">--}}
{{--                                    {{ __('Login') }}--}}
{{--                                </button>--}}

{{--                                @if (Route::has('password.request'))--}}
{{--                                    <a class="btn btn-link" href="{{ route('password.request') }}">--}}
{{--                                        {{ __('Forgot Your Password?') }}--}}
{{--                                    </a>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>

<div class="dim d-none"></div>
<div class="popup confirm d-none">
    <div class="confirm-txt">
        정확한 아이디를 입력하세요.
    </div>
    <div class="confirm-btn">
        <button class="btn w-100">확인</button>
    </div>
</div>
@endsection
