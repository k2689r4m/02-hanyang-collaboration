@extends('admin.layouts.admin')

@section('script')
{{--    <script>--}}
{{--        const confirmPopupOff = () => {--}}
{{--            document.getElementById('confirmPopup').remove();--}}
{{--        }--}}
{{--    </script>--}}

@endsection

@section('content')
    <div class="card-body">
        <div class="card-body card col-6 m-t-20">
            <h3 class="card-title border-bottom p-10">회원정보</h3>
            <form id="myForm" method="POST" action="{{ route('admin.memberDetailPro', ['userId' => $user->id]) }}">
                @csrf
                <table class="table table-input">
                    <tr>
                        <td><label>이메일</label> <input type="text" class="form-control" name="email" value="{{ $user->email }}" disabled /></td>
                        <td><label>휴대폰번호</label> <input type="text" class="form-control" name="contact" value="{{ $user->contact }}" disabled/></td>
                    </tr>
                    <tr>
                    </tr>
                    <tr>
                        <td><label>이름</label> <input type="text" class="form-control" name="name" value="{{ $user->name }}" disabled/></td>
                        <td><label>신분구분자</label>
                        @if($user->social != '')
                            <input type="text" class="form-control" value="@if($user->authority == 7)교수,단대장@elseif($user->authority == 2)교수@endif" readonly/></td>
                        @else
                            <select class="form-control" name="authority">
                                {{--임시로 일반은 authority 0번 관리자도 됨 막아야함 --}}
                                <option {{ $user->authority == 0 ? 'selected' : ''}} value="0">일반</option>
                                <option {{ $user->authority == 1 ? 'selected' : ''}} value="1">학생</option>
                                <option {{ $user->authority == 2 ? 'selected' : ''}} value="2">교수</option>
                                <option {{ $user->authority == 4 ? 'selected' : ''}} value="4">컨설턴트</option>
                                <option {{ $user->authority == 5 ? 'selected' : ''}} value="5">공동교수자/외부전문가</option>
                                <option {{ $user->authority == 6 ? 'selected' : ''}} value="6">조교</option>
                                <option {{ $user->authority == 7 ? 'selected' : ''}} value="7">단대장</option>
                                <option {{ $user->authority == 8 ? 'selected' : ''}} value="8">센터</option>
                                <option {{ $user->authority == 9 ? 'selected' : ''}} value="9">행정</option>
                            </select>
                        @endif


{{--                        <select class="form-control" name="authority">--}}
{{--                            @if($user->social != '')--}}
{{--                                <option @if($user->authority == 2) selected @endif value="2">교수</option>--}}
{{--                                <option @if($user->authority == 7) selected @endif value="7">교수, 단대장</option>--}}
{{--                            @else--}}
{{--                                --}}{{--임시로 일반은 authority 0번 관리자도 됨 막아야함 --}}
{{--                                <option @if($user->authority == 0) selected @endif value="0">일반</option>--}}
{{--                                <option @if($user->authority == 1) selected @endif value="0">학생</option>--}}
{{--                                <option @if($user->authority == 2) selected @endif value="2">교수</option>--}}
{{--                                <option @if($user->authority == 4) selected @endif value="4">컨설턴트</option>--}}
{{--                                <option @if($user->authority == 5) selected @endif value="5">공동교수자/외부전문가</option>--}}
{{--                                <option @if($user->authority == 6) selected @endif value="6">조교</option>--}}
{{--                                <option @if($user->authority == 9) selected @endif value="9">행정</option>--}}
{{--                            @endif--}}
{{--                        </select>--}}
                    </tr>
                    <tr>
                        <td>
                            <label>한양대구분자</label>
                            <input type="text" class="form-control"
                                   @if($user->social=='hanyang')value="hanyang"@else value=""@endif
                                   readonly/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>기초교육 대상자 여부</label>
                            <select name="basic" class="form-control">
                                <option value="0" >비대상자</option>
                                <option value="1" @if($user->basicTarget) selected @endif>대상자</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>컨설팅 대상자 여부</label>
                            <select name="consulting" class="form-control">
                                <option value="0">비대상자</option>
                                <option value="1" @if($user->consultingTarget) selected @endif>대상자</option>
                            </select>
                        </td>
                        <td>
                            <label>컨설팅 담당 컨설턴트</label>
                            <select class="form-control" name="consultant">
                                <option>선택</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                </table>
                <div class="text-center p-t-20">
                    <button class="btn btn-light" type="button" onclick="confirm('취소하시겠습니까?') ? location.href='{{ route('admin.memberView') }}' : ''">취소</button>
                    <button class="btn btn-primary" type="button" onclick="confirm('저장하시겠습니까?') ? document.getElementById('myForm').submit() : ''">확인</button>
                </div>
            </form>
        </div>
    </div>
@endsection