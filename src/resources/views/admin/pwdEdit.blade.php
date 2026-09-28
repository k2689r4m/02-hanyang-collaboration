@extends('admin.layouts.admin')
@section('content')
<form method="POST" action="{{ route('admin.pwdEdit') }}" enctype="multipart/form-data">
    @csrf
    <div class="pw-wrap border">
        <input type="hidden" name="key" value="{{ $key }}" />

        <p>비밀번호</p>
        <input type="password" class="form-control" name="password" placeholder="영문/숫자 혼합6~12글자" />
        @error('password')
        <p>{{ $message }}</p>
        @enderror

        <p class="m-t-30">비밀번호 확인</p>
        <input type="password" class="form-control" name="password_confirmation" placeholder="영문/숫자 혼합6~12글자" />
        @error('password_confirmation')
        <p>{{ $message }}</p>
        @enderror

        <div class="m-t-30">
            <button type="button" class="btn btn-outline-primary" onclick="location.href=`{{ route('admin.pwdCheckView') }}`">뒤로가기</button>
            <button class="btn btn-primary">확인</button>
        </div>
    </div>
</form>
@endsection