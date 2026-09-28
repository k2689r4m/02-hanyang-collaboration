@extends('layouts.layout')

@section('content')
<div class="container">
    <div class="find-pwd__con">
        <h3 class="con-tit">비밀번호 찾기</h3>
        <div class="input-wrap">
            <label class="input-label">아이디(이메일)</label>
            <div class="input">
                <input type="text" placeholder="ID@email.com" />
            </div>
            <label class="input-label">이름</label>
            <div class="input">
                <input type="text" />
            </div>
            <label class="input-label">휴대폰 번호</label>
            <div class="input">
                <input type="text" placeholder="01012341234" />
            </div>
        </div>
        <div class="t-center pt-10">
            <button class="btn btn-primary btn-line btn-lg mr-10">취소</button>
            <button class="btn btn-primary btn-lg">임시비밀번호 발송</button>
        </div>
    </div>
{{--    <div class="row justify-content-center">--}}
{{--        <div class="col-md-8">--}}
{{--            <div class="card">--}}
{{--                <div class="card-header">{{ __('Reset Password') }}</div>--}}

{{--                <div class="card-body">--}}
{{--                    @if (session('status'))--}}
{{--                        <div class="alert alert-success" role="alert">--}}
{{--                            {{ session('status') }}--}}
{{--                        </div>--}}
{{--                    @endif--}}

{{--                    <form method="POST" action="{{ route('password.email') }}">--}}
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

{{--                        <div class="form-group row mb-0">--}}
{{--                            <div class="col-md-6 offset-md-4">--}}
{{--                                <button type="submit" class="btn btn-primary">--}}
{{--                                    {{ __('Send Password Reset Link') }}--}}
{{--                                </button>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>
@endsection
