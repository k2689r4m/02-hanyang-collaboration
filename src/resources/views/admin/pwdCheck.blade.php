@extends('admin.layouts.admin')
@section('content')
<form method="POST" action="{{ route('admin.pwdCheck') }}" enctype="multipart/form-data">
    @csrf
    <div class="pw-wrap border">
        <p>비밀번호 확인</p>
        <input type="password" class="form-control" name="password" placeholder="영문/숫자 혼합6~12글자" />
        @error('password')
        <p>{{ $message }}</p>
        @enderror
        <div class="m-t-30">
            <button type="button" class="btn btn-outline-primary" onclick="location.href=`{{ route('admin.class1View') }}`">뒤로가기</button>
            <button class="btn btn-primary">확인</button>
        </div>
    </div>
</form>
@endsection